<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-08-08
 * Time: 13:21
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\ToolTag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/tags")
 */
class TagsController extends Controller {

    /**
     * @Route("/", name="admin_tags")
     */
    public function tags() {
        return $this->render('admin/tags/tags.html.twig');
    }

    /**
     * @Route("/tags-autocomplete", name="admin_tags_autocomplete")
     */
    public function tagsAutocomplete() {
        $repo = $this->getDoctrine()->getRepository(ToolTag::class);
        $tags = $repo->findAll();
        $tagsArr = array();
        foreach ($tags as $tag) {
            $tagsArr[] = $tag->getTag();
        }
        $respObj = new Response(json_encode($tagsArr));
        $respObj->headers->set('Content-Type', 'text/json');
        return $respObj;
    }

}