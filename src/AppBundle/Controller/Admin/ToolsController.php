<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tool;
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
        $repo = $this->getDoctrine()->getRepository(Tool::class);
        $tools = $repo->findAll();
        return $this->render('admin/tools/tools.html.twig', array('tools' => $tools));
    }

    /**
     * @Route("/addTool", name="admin_add_tool")
     */
    public function addTool(Request $request) {
        $paramArr = $request->request->all();
        if (count($paramArr) > 8) {
            $name = $request->request->get('tool_name');
            $model = $request->request->get('tool_model');
            $code = $request->request->get('tool_code');
            $descr = $request->request->get('tool_description');
            if ($name && $model && $code && $descr) {
                $entityManager = $this->getDoctrine()->getManager();
                $tool = new Tool();
                $tool->setName($paramArr['tool_name']);
                $tool->setModel($paramArr['tool_model']);
                $tool->setCode($paramArr['tool_code']);
                $tool->setDescription($paramArr['tool_description']);
                $tool->setShopLinks($paramArr['tool_links']);
                $tool->setOriginalPrice($paramArr['tool_price']);
                $tool->setAcquisitionDate($paramArr['tool_date']);

                $entityManager->persist($tool);
                $entityManager->flush();

                $this->addFlash('success', 'Tool created!');
                return $this->redirectToRoute('admin_tools');
            }
        }
        return $this->render('admin/tools/add_tool.html.twig');
    }

}