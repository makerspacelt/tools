<?php

namespace App\Controller\Admin;

use App\Entity\ToolPhotos;
use App\Entity\Tool;
use App\Form\Type\ToolType;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tools")
 */
class ToolsController extends AbstractController
{
    /** @var ToolsRepository */
    private $toolsRepository;

    public function __construct(ToolsRepository $toolsRepository)
    {
        $this->toolsRepository = $toolsRepository;
    }

    /**
     * @Route("/", name="admin_tools")
     */
    public function tools(): Response
    {
        return $this->render(
            'admin/tools/tools.html.twig',
            [
                'tools' => $this->toolsRepository->findAll(),
            ]
        );
    }

    /**
     * Generuojamas 11 skaitmenų kodas, atsitiktiniai skaičiai
     */
    private function generateToolCode(): string
    {
        do {
            $code = str_pad(intval(rand(1, 999999)), '6', '0', STR_PAD_LEFT);
        } while ($this->toolsRepository->findOneBy(['code' => $code]));

        return $code;
    }

    /**
     * @Route("/addTool", name="admin_add_tool")
     * @param Request $request
     * @return Response
     */
    public function addTool(Request $request): Response
    {
        $tool = new Tool();
        $form = $this->createForm(ToolType::class, $tool);
        $form->get('code')->setData($this->generateToolCode());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->getData()->getTags() as $tag) {
                $tool->addTag($tag);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($tool);
            $em->flush();
            $this->addFlash('success', 'Tool created!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/add_tool.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/editTool/{id}", name="admin_edit_tool")
     * @param Request $request
     * @param Tool    $tool
     * @return Response
     */
    public function editTool(Request $request, Tool $tool): Response
    {
        $form = $this->createForm(ToolType::class, $tool);
        $currentTags = $tool->getTags()->toArray();
        $form->handleRequest($request);
//        echo '<pre>'; var_dump($form->getData()); die();
        /*
         * Čia turime praleisti $form->isValid() nes nuotraukų įkėlimas renderinamas atskirai,
         * ne per formo kūrimą
         */
        if ($form->isSubmitted()) {
            $formTool = $form->getData();
//            echo '<pre>'; var_dump($formTool); die();
            $repo = $this->getDoctrine()->getManager();

            //----------------- tag block ------------------
            $submittedTags = [];
            if ($formTool->getTags()) {
                $submittedTags = $formTool->getTags()->toArray();
            }

            $removedTags = array_udiff($currentTags, $submittedTags, [$this, 'arrayCompare']);
            $addedTags = array_udiff($submittedTags, $currentTags, [$this, 'arrayCompare']);

            foreach ($removedTags as $tag) {
                $tag->removeTool($tool);
                if ($tag->getTools()->count() == 0) {
                    $repo->remove($tag);
                }
            }

            foreach ($addedTags as $tag) {
                $tag->addTool($tool);
            }
            //----------------- log block ------------------
            // reikia panaikinti tuščią įvedimo lauką jeigu forma buvo siųsta nieko nekeitus log'uose
            foreach ($formTool->getLogs() as $log) {
                if (!$log->getLog()) {
                    $formTool->removeLog($log);
                }
            }
            //----------------- param block ----------------
//            echo '<pre>'; var_dump($formTool->getParams()->toArray()); die();
            //----------------------------------------------

            //----------------- photo block ----------------
            echo '<pre>';
            var_dump($formTool->getPhotos()->toArray());
            die();
            //----------------------------------------------

            $repo->flush();
            $this->addFlash('success', 'Tool modified!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/edit_tool.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delTool", name="admin_del_tool")
     * @param Request $request
     * @return Response
     */
    public function deleteTool(Request $request): Response
    {
        $toolid = $request->request->get('tool_id');
        if ($toolid != null) {
            $tool = $this->toolsRepository->find($toolid);
            if ($tool) {
                $repo = $this->getDoctrine()->getManager();

                // pašalinam tag'ą tik jeigu jis nenaudojamas niekur kitur
                foreach ($tool->getTags() as $tag) {
                    if ($tag->getTools()->count() <= 1) {
                        $repo->remove($tag);
                    }
                }

                foreach ($tool->getParams() as $param) {
                    $repo->remove($param);
                }

                foreach ($tool->getLogs() as $log) {
                    $repo->remove($log);
                }

                $repo->remove($tool);
                $repo->flush();
                $this->addFlash('success', sprintf('Tool "%s" removed!', $tool->getName() . ' ' . $tool->getModel()));
            } else {
                $this->addFlash('error', 'Tool doesn\'t exist!');
            }
        }
        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/uploadPhotos", name="upload_photos")
     * @param Request $request
     * @return Response
     */
    public function uploadPhotos(Request $request): Response
    {
        $output = ['uploaded' => false];

        $file = $request->files->get('photo');
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();

        $uploadDir = $this->get('kernel')->getRootDir() . '/../web/res/uploads/';
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        if ($file->move($uploadDir, $fileName)) {
            $em = $this->getDoctrine()->getManager();
            $photoEntity = new ToolPhotos();
            $photoEntity->setFileName($fileName);
            $photoEntity->setTool($this->toolsRepository->find(474));
            $em->persist($photoEntity);
            $em->flush();
            $output['uploaded'] = true;
            $output['filename'] = $fileName;
        }

        return new JsonResponse($output);
    }
}
