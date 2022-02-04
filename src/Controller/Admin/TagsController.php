<?php

namespace App\Controller\Admin;

use App\Entity\ToolTag;
use App\Repository\TagsRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tags")
 */
class TagsController extends AbstractController
{
    private TagsRepository $tagsRepository;

    public function __construct(TagsRepository $tagsRepository)
    {
        $this->tagsRepository = $tagsRepository;
    }

    /**
     * @Route("/", name="admin_tags")
     */
    public function tags(): Response
    {
        $tags = $this->tagsRepository->findAll();
        $tagArr = [];
        foreach ($tags as $tag) {
            $tagArr[] = [
                'id'         => $tag->getId(),
                'tag'        => $tag->getTag(),
                'usageCount' => $tag->getTools()->count(),
            ];
        }
        return $this->render(
            'admin/tags/tags.html.twig',
            [
                'tags' => $tagArr,
            ]
        );
    }

    /**
     * @Route("/tags-autocomplete", name="admin_tags_autocomplete")
     * @param Request $request
     * @return Response
     */
    public function tagsAutocomplete(Request $request): Response
    {
        if ($request->query->has('term')) {
            $tags = $this->tagsRepository->searchTags($request->query->get('term'));
        } else {
            $tags = $this->tagsRepository->findAll();
        }

        $tagsArr = [];
        foreach ($tags as $tag) {
            $tagsArr[] = $tag->getTag();
        }

        return new JsonResponse($tagsArr, 200, ['Content-Type' => 'text/json']);
    }

    /**
     * @Route("/editTag/{id}", name="admin_edit_tag")
     * @param Request $request
     * @param ToolTag $toolTag
     * @return Response
     * @throws ORMException
     */
    public function editTag(Request $request, ToolTag $toolTag): Response
    {
        $form = $this->createFormBuilder($toolTag)
            ->add('tag', TextType::class, ['required' => true])
            ->add('save', SubmitType::class, ['label' => 'Submit'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formToolTag = $form->getData();
            $this->tagsRepository->save($formToolTag);
            $this->addFlash('success', 'Tag edited!');
            return $this->redirectToRoute('admin_tags');
        }

        return $this->render('admin/tags/edit_tag.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/deleteTag", name="admin_delete_tag")
     * @param Request $request
     * @return Response
     * @throws ORMException
     */
    public function deleteTag(Request $request): Response
    {
        if ($tagId = $request->request->has('tag_id')) {
            $tag = $this->tagsRepository->find($tagId);
            if ($tag) {
                $this->tagsRepository->remove($tag);
                $this->addFlash('success', 'Tag removed!');
            } else {
                $this->addFlash('error', sprintf("Tag '%s' not found.", $tagId));
            }
        } else {
            $this->addFlash('error', 'No tag_id in request.');
        }

        return $this->redirectToRoute('admin_tags');
    }
}
