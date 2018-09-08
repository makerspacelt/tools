<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolLog;
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
//        echo '<pre>'; print_r($paramArr); die();
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

                foreach ($paramArr['tool_repair_log'] as $entry) {
                    $toolLog = new ToolLog();
                    $toolLog->setLog($entry);
                    $tool->addLog($toolLog);
                    $entityManager->persist($toolLog);
                }

                $entityManager->persist($tool);
                $entityManager->flush();

                $this->addFlash('success', 'Tool created!');
                return $this->redirectToRoute('admin_tools');
            }
        }
        return $this->render('admin/tools/add_tool.html.twig');
    }

    /**
     * @Route("/editTool", name="admin_edit_tool")
     */
    public function editTool(Request $request) {
        $toolid = $request->request->get('tool_id');
        if ($toolid && $request->request->has('edit_token')) { // čia ateina redaguoti info
            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
            $rtnArr = array('tool' => $tool);
            if ($tool) {
                $rtnArr['logs'] = $tool->getLogs();
            }
            return $this->render('admin/tools/edit_tool.html.twig', $rtnArr);
        } else if ($request->request->count() >= 4) { // pakeista info buvo submitinta
            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
            if ($tool) {
                $name = $request->request->get('tool_name');
                $model = $request->request->get('tool_model');
                $code = $request->request->get('tool_code');
                $descr = $request->request->get('tool_description');
                if ($name && $model && $code && $descr) {
                    $paramArr = $request->request->all();
                    $entityManager = $this->getDoctrine()->getManager();
                    $tool->setName($paramArr['tool_name']);
                    $tool->setModel($paramArr['tool_model']);
                    $tool->setCode($paramArr['tool_code']);
                    $tool->setDescription($paramArr['tool_description']);
                    $tool->setShopLinks($paramArr['tool_links']);
                    $tool->setOriginalPrice($paramArr['tool_price']);
                    $tool->setAcquisitionDate($paramArr['tool_date']);

                    // TODO: padaryti log'ų atnaujinimą ir naujų įrašų išsaugojimą nekuriant dublikatų
                    foreach ($paramArr['tool_repair_log'] as $entry) {
                        $toolLog = new ToolLog();
                        $toolLog->setLog($entry);
                        $tool->addLog($toolLog);
                        $entityManager->persist($toolLog);
                    }

                    $entityManager->flush();
                }
                $this->addFlash('success', 'Tool modified!');
            }
        }
        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/delTool", name="admin_del_tool")
     */
    public function deleteTool(Request $request) {
        $toolid = $request->request->get('toolid');
        if ($toolid != null) {
            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
            if ($tool) {
                $repo = $this->getDoctrine()->getManager();
                $repo->remove($tool);
                $repo->flush();
                $this->addFlash('success', sprintf('Tool "%s" removed!', $tool->getName().' '.$tool->getModel()));
            } else {
                $this->addFlash('error', 'Tool doesn\'t exist!');
            }
        }
        return $this->redirectToRoute('admin_tools');
    }

}
