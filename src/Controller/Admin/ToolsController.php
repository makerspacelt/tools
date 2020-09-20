<?php

namespace App\Controller\Admin;

use App\Entity\ToolPhoto;
use App\Entity\Tool;
use App\Entity\ToolTag;
use App\Form\Type\ToolType;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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

        /** @var UploadedFile $file */
        foreach ($form->get('photos')->getData() as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = transliterator_transliterate(
                'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                $originalFilename
            );
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $photo = new ToolPhoto();
                $photo->setFileName($newFilename);
                $photo->setTool($tool);
                $tool->addPhoto($photo);
                $manager->persist($photo);
            } catch (FileException $e) {
                $this->addFlash(
                    'danger',
                    sprintf(
                        'Failed to upload photo "%s": %s',
                        $file->getClientOriginalName(),
                        $e->getMessage()
                    )
                );
            }
        }

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

        $removedTags = $this->diffTagSets($currentTags, $submittedTags);
        $addedTags = $this->diffTagSets($submittedTags, $currentTags);

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
     * @param ToolTag[] $set1
     * @param ToolTag[] $set2
     * @return ToolTag[]
     */
    private function diffTagSets(array $set1, array $set2): array
    {
        return array_udiff(
            $set1,
            $set2,
            function (ToolTag $arr1, ToolTag $arr2) {
                return $arr1->getId() - $arr2->getId();
            }
        );
    }
}
