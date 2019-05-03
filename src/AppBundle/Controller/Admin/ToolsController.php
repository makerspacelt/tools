<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolLog;
use AppBundle\Entity\ToolParameter;
use AppBundle\Entity\ToolTag;
use AppBundle\Form\DataTransformer\TagTransformer;
use AppBundle\Form\TagType;
use AppBundle\Form\Type\ToolType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * Generuojamas 11 skaitmenų kodas, atsitiktiniai skaičiai
     */
    private function generateToolCode() {
        $repo = $this->getDoctrine()->getRepository(Tool::class);
        do {
            $code = str_pad(intval(rand(1, 999999)), '6', '0', STR_PAD_LEFT);
        } while ($repo->findOneBy(array('code' => $code)));
        return $code;
    }

    private function generateForm(Tool $tool) {
        return $this->createFormBuilder($tool)->
        add('name', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('model', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('code', TextType::class, ['required' => true, 'attr' => ['class' => 'mb-3']])->
        add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'mb-3']])->
//            add('photos', FileType::class)->
        add('tags', TagType::class, ['required' => false, 'attr' => ['class' => 'mb-3']])->
        add('shoplinks', TextareaType::class, ['required' => false, 'label' => 'Where to buy?'])->
        add('originalprice', TextType::class, ['required' => false, 'label' => 'Original price'])->
        add('acquisitiondate', DateType::class, ['required' => false, 'widget' => 'single_text', 'label' => 'Acquisition date'])->
        add('save', SubmitType::class, ['label' => 'Submit'])->
        getForm();
    }

    /**
     * @Route("/addTool", name="admin_add_tool")
     */
    public function addTool(Request $request) {
        $tool = new Tool();
        $form = $this->generateForm($tool);
        $form->get('code')->setData($this->generateToolCode());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formTool = $form->getData();
            $em = $this->getDoctrine()->getManager();

            foreach ($tool->getTags() as $tag) {
                $tag->setTool($tool);
            }

            $em->persist($formTool);
            $em->flush();
            $this->addFlash('success', 'Tool created!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/add_tool.html.twig', ['form' => $form->createView()]);

//        $paramArr = $request->request->all();
//        if (count($paramArr) > 8) {
//            $name = $request->request->get('tool_name');
//            $model = $request->request->get('tool_model');
//            $code = $request->request->get('tool_code');
//            $descr = $request->request->get('tool_description');
//            if ($name && $model && $code && $descr) {
//                $entityManager = $this->getDoctrine()->getManager();
//                $tool = new Tool();
//                $tool->setName($paramArr['tool_name']);
//                $tool->setModel($paramArr['tool_model']);
//                $tool->setCode($paramArr['tool_code']);
//                $tool->setDescription($paramArr['tool_description']);
//                $tool->setShopLinks($paramArr['tool_links']);
//                $tool->setOriginalPrice($paramArr['tool_price']);
//                $tool->setAcquisitionDate($paramArr['tool_date']);
//
//                // tag'ai turi būti unikalūs
//                $repo = $this->getDoctrine()->getRepository(ToolTag::class);
//                foreach (explode(',', $paramArr['tool_tags']) as $tag) {
//                    $dbTag = $repo->findOneBy(array('tag' => $tag));
//                    if ($dbTag) {
//                        $tool->addTag($dbTag);
//                    } else {
//                        $toolTag = new ToolTag();
//                        $tool->addTag($toolTag);
//                        $toolTag->setTag(trim(strtolower($tag)));
//                        $entityManager->persist($toolTag);
//                    }
//                }
//
//                foreach ($paramArr['tool_param'] as $param) {
//                    if (!empty(trim($param['name'])) && !empty(trim($param['value']))) {
//                        $toolParam = new ToolParameter();
//                        $toolParam->setName(trim($param['name']));
//                        $toolParam->setValue(trim($param['value']));
//                        $tool->addParam($toolParam);
//                        $entityManager->persist($toolParam);
//                    }
//                }
//
//                foreach ($paramArr['tool_repair_log'] as $entry) {
//                    if (!empty(($entry))) {
//                        $toolLog = new ToolLog();
//                        $toolLog->setLog(trim($entry));
//                        $tool->addLog($toolLog);
//                        $entityManager->persist($toolLog);
//                    }
//                }
//
//                $entityManager->persist($tool);
//                $entityManager->flush();
//
//                $this->addFlash('success', 'Tool created!');
//                return $this->redirectToRoute('admin_tools');
//            }
//        }
//        return $this->render('admin/tools/add_tool.html.twig', array('code' => $this->generateToolCode()));
    }

    /**
     * @Route("/editTool/{id}", name="admin_edit_tool")
     */
    public function editTool(Request $request, Tool $tool) {
        $form = $this->generateForm($tool);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formTool = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($formTool);
            $em->flush();
            $this->addFlash('success', 'Tool modified!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/edit_tool.html.twig', ['form' => $form->createView()]);

//        $toolid = $request->request->get('tool_id');
//        if ($toolid && $request->request->has('edit_token')) { // čia ateina redaguoti info
//            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
//            $rtnArr = array('tool' => $tool);
//            if ($tool) {
//                $rtnArr['tags'] = $tool->getTagsArray();
//                $rtnArr['params'] = $tool->getParams();
//                $rtnArr['logs'] = $tool->getLogs();
//            }
//            return $this->render('admin/tools/edit_tool.html.twig', $rtnArr);
//        } else if ($request->request->count() >= 4) { // pakeista info buvo submitinta
//            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
//            if ($tool) {
//                $name = $request->request->get('tool_name');
//                $model = $request->request->get('tool_model');
//                $code = $request->request->get('tool_code');
//                $descr = $request->request->get('tool_description');
//                if ($name && $model && $code && $descr) {
//                    $paramArr = $request->request->all();
//                    $entityManager = $this->getDoctrine()->getManager();
//                    $tool->setName($paramArr['tool_name']);
//                    $tool->setModel($paramArr['tool_model']);
//                    $tool->setCode($paramArr['tool_code']);
//                    $tool->setDescription($paramArr['tool_description']);
//                    $tool->setShopLinks($paramArr['tool_links']);
//                    $tool->setOriginalPrice($paramArr['tool_price']);
//                    $tool->setAcquisitionDate($paramArr['tool_date']);
//
//                    // TODO: [insert tool tag edit code block here]
//
//                    // TODO: [insert tool param edit code block here]
//
//                    // TODO: padaryti log'ų atnaujinimą ir naujų įrašų išsaugojimą nekuriant dublikatų
//
//                    $entityManager->flush();
//                }
//                $this->addFlash('success', 'Tool modified!');
//            }
//        }
//        return $this->redirectToRoute('admin_tools');
    }

    /**
     * @Route("/delTool", name="admin_del_tool")
     */
    public function deleteTool(Request $request) {
        $toolid = $request->request->get('tool_id');
        if ($toolid != null) {
            $tool = $this->getDoctrine()->getRepository(Tool::class)->find($toolid);
            if ($tool) {
                $repo = $this->getDoctrine()->getManager();

                // pašalinam tag'ą tik jeigu jis nenaudojamas niekur kitur
                foreach ($tool->getTags() as $tag) {
                    if ($tag->getTools()->count() <= 1) {
                        $repo->remove($tag);
                    }
                }

                foreach ($tool->getParams() as $param) {
                    $repo->remove($param);
                }

                foreach ($tool->getLogs() as $log) {
                    $repo->remove($log);
                }

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
