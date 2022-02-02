<?php

namespace App\Controller\Admin;

use App\Entity\ToolPhoto;
use App\Entity\Tool;
use App\Form\Type\ToolType;
use App\Form\Type\ToolUpdateType;
use App\Repository\ToolsRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


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
        $tool->setCode($this->generateToolCode());
        $form = $this->createForm(ToolType::class, $tool)->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            foreach ($form->getErrors() as $key => $value) {
                $this->addFlash('danger', $value->getMessage());
            }
            return $this->render('admin/tools/add_tool.html.twig', ['form' => $form->createView()]);
        }

        $this->processUploadedPhotos($tool, $form->get('new_photos')->getData());
        $this->processUploadedInstruction($tool, $form->get('instructionsPdf')->getData());

        try {
            $this->toolsRepository->save($tool);
            $this->addFlash(
                'success',
                sprintf('Tool "%s" saved!', $tool->getName() . ' ' . $tool->getModel())
            );
        } catch (ORMException $e) {
            $this->addFlash(
                'danger',
                sprintf('Failed to save tool "%s"!', $tool->getName() . ' ' . $tool->getModel())
            );
            $this->get('logger')->log('error', $e->getMessage());
        }

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
        $form = $this->createForm(ToolUpdateType::class, $tool)->handleRequest($request);
        if (!$form->isSubmitted() || !$form->isValid()) {
            foreach ($form->getErrors() as $key => $value) {
                $this->addFlash('danger', $value->getMessage());
            }
            return $this->render('admin/tools/edit_tool.html.twig', ['form' => $form->createView()]);
        }

        $this->processUploadedPhotos($tool, $form->get('new_photos')->getData());
        $this->processUploadedInstruction($tool, $form->get('instructionsPdf')->getData());

        try {
            $this->toolsRepository->update($tool);
            $this->addFlash(
                'success',
                sprintf('Tool "%s" updated!', $tool->getName() . ' ' . $tool->getModel())
            );
        } catch (ORMException $e) {
            $this->addFlash(
                'danger',
                sprintf('Failed to update tool "%s"!', $tool->getName() . ' ' . $tool->getModel())
            );
            $this->get('logger')->log('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/delete/{id}", name="admin_del_tool", methods={"POST"})
     * @param Tool $tool
     * @return Response
     */
    public function deleteTool(Tool $tool): Response
    {
        try {
            $this->toolsRepository->remove($tool);
            $this->addFlash(
                'success',
                sprintf('Tool "%s" removed!', $tool->getName() . ' ' . $tool->getModel())
            );
        } catch (ORMException $e) {
            $this->addFlash(
                'danger',
                sprintf('Failed to remove tool "%s"!', $tool->getName() . ' ' . $tool->getModel())
            );
            $this->get('logger')->log('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @param Tool           $tool
     * @param UploadedFile[] $files
     */
    private function processUploadedPhotos(Tool $tool, array $files): void
    {
        foreach ($files as $file) {
            $newFilename = $this->generateFileName($file);

            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $photo = new ToolPhoto();
                $photo->setFileName($newFilename);
                $photo->setTool($tool);
                $tool->addPhoto($photo);
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
    }

    private function processUploadedInstruction(Tool $tool, ?UploadedFile $file): void
    {
        if (is_null($file)) {
            return;
        }

        $newFilename = $this->generateFileName($file);
        $instructionsDir = $this->getParameter('instructions_directory');

        try {
            $file->move($instructionsDir, $newFilename);

            if (!is_null($tool->getInstructionsPdf())) {
                // Delete old instructions
                unlink($instructionsDir . DIRECTORY_SEPARATOR . $tool->getInstructionsPdf());
            }

            $tool->setInstructionsPdf($newFilename);
        } catch (FileException $e) {
            $this->addFlash(
                'danger',
                sprintf(
                    'Failed to upload instruction "%s": %s',
                    $file->getClientOriginalName(),
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Generate unique, random code of 6 digits
     */
    private function generateToolCode(): string
    {
        // TODO: find out if it really needs to be random. Maybe padded row id from db can be used.
        do {
            $code = str_pad(intval(rand(1, 999999)), '6', '0', STR_PAD_LEFT);
        } while ($this->toolsRepository->findOneBy(['code' => $code]));

        return $code;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    private function generateFileName(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
            $originalFilename
        );

        return $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
    }
}
