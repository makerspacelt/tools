<?php

namespace AppBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/tools")
 */
class ToolsController extends Controller {

    /**
     * @Route("/", name="admin_tools")
     */
    public function tools() {
        return $this->render('admin/tools/tools.html.twig');
    }

    /**
     * @Route("/addTool", name="admin_add_tool")
     */
    public function addTool(Request $request) {
        $paramArr = $request->request->all();
        if (count($paramArr) > 0) {
            echo '<pre>'; print_r($paramArr); die();
        }
        return $this->render('admin/tools/add_tool.html.twig');
    }

}