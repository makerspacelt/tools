<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolTag;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Forms;

/**
 * @Route("/tags")
 */
class TagsController extends Controller {

    /**
     * @Route("/", name="admin_tags")
     */
    public function tags() {
        $repo = $this->getDoctrine()->getRepository(ToolTag::class);
        $tags = $repo->findAll();
        $tagArr = array();
        foreach ($tags as $tag) {
            $tagArr[] = array(
                'id' => $tag->getId(),
                'tag' => $tag->getTag(),
                'usageCount' => $tag->getTools()->count()
            );
        }
        return $this->render('admin/tags/tags.html.twig', array('tags' => $tagArr));
    }

    /**
     * @Route("/tags-autocomplete", name="admin_tags_autocomplete")
     */
    public function tagsAutocomplete(Request $request) {
        $term = $request->query->get('term', null);
        $repo = $this->getDoctrine()->getRepository(ToolTag::class);
        if ($term) {
            $tags = $repo->searchTags($term);
        } else {
            $tags = $repo->findAll();
        }
        $tagsArr = array();
        foreach ($tags as $tag) {
            $tagsArr[] = $tag->getTag();
        }
        $respObj = new Response(json_encode($tagsArr));
        $respObj->headers->set('Content-Type', 'text/json');
        return $respObj;
    }

    /**
     * @Route("/editTag", name="admin_edit_tag")
     */
    public function editTag(Request $request) {
        if ($request->request->has('edit_token')) {
            $reqArr = $request->request->all();
            return $this->render('admin/tags/edit_tag.html.twig', $reqArr);
        } else {
            $reqArr = $request->request->all();
            $repo = $this->getDoctrine()->getRepository(ToolTag::class);
            $tag = $repo->find($reqArr['tag_id']);
            if ($tag) {
                $tag->setTag(trim(strtolower($reqArr['tag'])));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                $this->addFlash('success', 'Tag edited!');
            }
            return $this->redirectToRoute('admin_tags');
        }
    }

    /**
     * @Route("/deleteTag", name="admin_delete_tag")
     */
    public function deleteTag(Request $request) {
        if ($request->request->has('tag_id')) {
            $tag = $this->getDoctrine()->getRepository(ToolTag::class)->find(
                $request->request->get('tag_id')
            );
            if ($tag) {
                $repo = $this->getDoctrine()->getManager();
                $repo->remove($tag);
                $repo->flush();
                $this->addFlash('success', 'Tag removed!');
            }
            return $this->redirectToRoute('admin_tags');
        }
    }
}