<?php

namespace App\Controller\Admin;

use App\Entity\ToolPhotos;
use App\Entity\Tool;
use App\Form\Type\LogType;
use App\Form\Type\ParamType;
use App\Form\Type\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/tools")
 */
class ToolsController extends AbstractController
{
    /**
     * @Route("/", name="admin_tools")
     */
    public function tools() {
        $repo = $this->getDoctrine()->getRepository(Tool::class);
        $tools = $repo->findAll();
        return $this->render('admin/tools/tools.html.twig', array('tools' => $tools));
    }

    /**
     * Generuojamas 11 skaitmenų kodas, atsitiktiniai skaičiai
     */
    private function generateToolCode() {
        $repo = $this->getDoctrine()->getRepository(Tool::class);
        do {
            $code = str_pad(intval(rand(1, 999999)), '6', '0', STR_PAD_LEFT);
        } while ($repo->findOneBy(array('code' => $code)));
        return $code;
    }

    /**
     * Funkcija skirta naudoti kaip callback'as array_udiff funkcijai
     */
    private function arrayCompare($arr1, $arr2) {
        return $arr1->getId() - $arr2->getId();
    }

    private function generateForm(Tool $tool) {
        return $this->createFormBuilder($tool)->
        add('name', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('model', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('code', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'mb-3']])->
        add('tags', TagType::class, ['required' => false, 'attr' => ['class' => 'mb-3']])->
        add('params', CollectionType::class, ['required' => false, 'entry_type' => ParamType::class, 'allow_add' => true, 'allow_delete' => true, 'label' => false, 'by_reference' => false])->
        add('logs', CollectionType::class, ['required' => false, 'entry_type' => LogType::class, 'allow_add' => true, 'allow_delete' => true, 'label' => false,'by_reference' => false])->
        add('shoplinks', TextareaType::class, ['required' => false, 'label' => 'Where to buy?'])->
        add('originalprice', TextType::class, ['required' => false, 'label' => 'Original price'])->
        add('acquisitiondate', DateType::class, ['required' => false, 'widget' => 'single_text', 'label' => 'Acquisition date'])->
        add('save', SubmitType::class, ['label' => 'Submit'])->
        getForm();
    }

    /**
     * @Route("/addTool", name="admin_add_tool")
     */
    public function addTool(Request $request) {
        $tool = new Tool();
        $form = $this->generateForm($tool);
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
     */
    public function editTool(Request $request, Tool $tool) {
        $form = $this->generateForm($tool);
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
            $submittedTags = array();
            if ($formTool->getTags()) {
                $submittedTags = $formTool->getTags()->toArray();
            }

            $removedTags = array_udiff($currentTags, $submittedTags, array($this, 'arrayCompare'));
            $addedTags = array_udiff($submittedTags, $currentTags, array($this, 'arrayCompare'));

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
            echo '<pre>'; var_dump($formTool->getPhotos()->toArray()); die();
            //----------------------------------------------

            $repo->flush();
            $this->addFlash('success', 'Tool modified!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/edit_tool.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delTool", name="admin_del_tool")
     */
    public function deleteTool(Request $request) {
        $toolid = $request->request->get('tool_id');
        if ($toolid != null) {
            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
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
                $this->addFlash('success', sprintf('Tool "%s" removed!', $tool->getName().' '.$tool->getModel()));
            } else {
                $this->addFlash('error', 'Tool doesn\'t exist!');
            }
        }
        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/uploadPhotos", name="upload_photos")
     */
    public function uploadPhotos(Request $request) {
        $output = array('uploaded' => false);

        $file = $request->files->get('photo');
        $fileName = uniqid().'.'.$file->getClientOriginalExtension();

        $uploadDir = $this->get('kernel')->getRootDir() . '/../web/res/uploads/';
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        if ($file->move($uploadDir, $fileName)) {
            $em = $this->getDoctrine()->getManager();
            $photoEntity = new ToolPhotos();
            $photoEntity->setFileName($fileName);
            $photoEntity->setTool($em->getRepository(Tool::class)->find(474));
            $em->persist($photoEntity);
            $em->flush();
            $output['uploaded'] = true;
            $output['filename'] = $fileName;
        }

        return new JsonResponse($output);
    }
}
