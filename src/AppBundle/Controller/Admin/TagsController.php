<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-08-08
 * Time: 13:21
 */

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
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

}