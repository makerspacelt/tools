<?php

namespace App\Controller\Admin;

use App\Entity\ToolPhoto;
use App\Entity\Tool;
use App\Form\Type\ToolType;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/add", name="admin_add_tool")
     * @param Request $request
     * @return Response
     */
    public function addTool(Request $request): Response
    {
        $tool = new Tool();
        $form = $this->createForm(ToolType::class, $tool)->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('admin/tools/add_tool.html.twig', ['form' => $form->createView()]);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($tool);
        $manager->flush();
        $this->addFlash('success', 'Tool saved!');

        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/edit/{id}", name="admin_edit_tool")
     * @param Request $request
     * @param Tool    $tool
     * @return Response
     */
    public function editTool(Request $request, Tool $tool): Response
    {
        $currentTags = $tool->getTags()->toArray();
        $form = $this->createForm(ToolType::class, $tool)->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('admin/tools/edit_tool.html.twig', ['form' => $form->createView()]);
        }

        // TODO: make a copy of old value and do updating in clearer way

        $manager = $this->getDoctrine()->getManager();

        //----------------- tag block ------------------
        $submittedTags = [];
        if ($tool->getTags()) {
            $submittedTags = $tool->getTags()->toArray();
        }

        $removedTags = array_udiff($currentTags, $submittedTags, [$this, 'arrayCompare']);
        $addedTags = array_udiff($submittedTags, $currentTags, [$this, 'arrayCompare']);

        foreach ($removedTags as $tag) {
            $tag->removeTool($tool);
            if ($tag->getTools()->count() == 0) {
                $manager->remove($tag);
            }
        }

        foreach ($addedTags as $tag) {
            $tag->addTool($tool);
        }

        //----------------- log block ------------------
        // reikia panaikinti tuščią įvedimo lauką jeigu forma buvo siųsta nieko nekeitus log'uose
        // TODO ^
        foreach ($tool->getLogs() as $log) {
            if (!$log->getLog()) {
                $tool->removeLog($log);
            }
        }

        //----------------- param block ----------------
        // TODO
        //----------------------------------------------

        $manager->flush();
        $this->addFlash('success', 'Tool updated!');

        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/delete/{id}", name="admin_del_tool", methods={"POST"})
     * @param Tool $tool
     * @return Response
     */
    public function deleteTool(Tool $tool): Response
    {
        $manager = $this->getDoctrine()->getManager();

        // TODO: move to repository
        // Remove tags from database only if they have no reference to other tools
        foreach ($tool->getTags() as $tag) {
            if ($tag->getTools()->count() <= 1) {
                $manager->remove($tag);
            }
        }

        // TODO: solve with cascading
        foreach ($tool->getParams() as $param) {
            $manager->remove($param);
        }

        // TODO: solve with cascading
        foreach ($tool->getLogs() as $log) {
            $manager->remove($log);
        }

        $manager->remove($tool);
        $manager->flush();
        $this->addFlash('success', sprintf('Tool "%s" removed!', $tool->getName() . ' ' . $tool->getModel()));

        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/upload-photo/{id}", name="upload_photos")
     * @param Request $request
     * @param Tool    $tool
     * @return Response
     */
    public function uploadPhotos(Request $request, Tool $tool): Response
    {
        // TODO: add separate page for already existing tool to add pictures

        /** @var UploadedFile $file */
        $file = $request->files->get('photo');
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // This is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getParameter('images_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            return new JsonResponse(['uploaded' => false]);
        }

        $manager = $this->getDoctrine()->getManager();
        $photo = new ToolPhoto();
        $photo->setFileName($newFilename);
        $photo->setTool($tool);
        $manager->persist($photo);
        $manager->flush();

        return new JsonResponse(
            [
                'uploaded' => true,
                'filename' => $newFilename,
            ]
        );
    }

    private function arrayCompare($arr1, $arr2)
    {
        return $arr1->getId() - $arr2->getId();
    }
}
