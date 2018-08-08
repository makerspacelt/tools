<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-08-08
 * Time: 13:21
 */

namespace AppBundle\Controller\Admin;

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
        $dataArray = array('graztas', 'plaktukas', 'stakles', 'kaltas');
        $respObj = new Response(json_encode($dataArray));
        $respObj->headers->set('Content-Type', 'text/json');
        return $respObj;
    }

}