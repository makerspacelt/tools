<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolLog;
use AppBundle\Entity\ToolParameter;
use AppBundle\Entity\ToolTag;
use AppBundle\Form\DataTransformer\TagTransformer;
use AppBundle\Form\Type\TagType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

    /**
     * Funkcija skirta naudoti kaip callback'as array_udiff funkcijai
     */
    private function arrayCompare($arr1, $arr2) {
        return $arr1->getId() - $arr2->getId();
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
            foreach ($form->getData()->getTags() as $tag) {
                $tool->addTag($tag);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($tool);
            $em->flush();
            $this->addFlash('success', 'Tool created!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/add_tool.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/editTool/{id}", name="admin_edit_tool")
     */
    public function editTool(Request $request, Tool $tool) {
        $form = $this->generateForm($tool);
        $currentTags = $tool->getTags()->toArray();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formTool = $form->getData();
            $repo = $this->getDoctrine()->getManager();

            $submittedTags = array();
            if ($formTool->getTags()) {
                $submittedTags = $formTool->getTags()->toArray();
            }

            $removedTags = array_udiff($currentTags, $submittedTags, array($this, 'arrayCompare'));
            $addedTags = array_udiff($submittedTags, $currentTags, array($this, 'arrayCompare'));

            foreach ($removedTags as $tag) {
                $tag->removeTool($tool);
                if ($tag->getTools()->count() == 0) {
                    $repo->remove($tag);
                }
            }

            foreach ($addedTags as $tag) {
                $tag->addTool($tool);
            }

            $repo->flush();
            $this->addFlash('success', 'Tool modified!');
            return $this->redirectToRoute('admin_tools');
        }

        return $this->render('admin/tools/edit_tool.html.twig', ['form' => $form->createView()]);
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
                // TODO: patikrinti kaip bus su cascade delete
                foreach ($tool->getParams() as $param) {
                    $repo->remove($param);
                }
                // TODO: patikrinti kaip bus su cascade delete
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
