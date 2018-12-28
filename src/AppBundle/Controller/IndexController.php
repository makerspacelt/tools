<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolTag;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller {

    private $tags;

    public function __construct(EntityManager $em) {
        // get tags here
        $repo = $em->getRepository(ToolTag::class);
        $this->tags = $repo->findAll();
    }

    /**
     * @Route("/", name="index_page")
     */
    public function index() {
        // get tools
        $repo = $this->getDoctrine()->getRepository(Tool::class);
        $tools = $repo->findAll();

        return $this->render('index.html.twig', array('tags' => $this->tags, 'tools' => $tools));
    }

    /**
     * @Route("/filter", name="filter_by_tags", methods={"POST"})
     */
    public function filterByTags(Request $request) {
        if ($request->request->has('tags')) {
            $tags = $request->request->get('tags', array());
            $repo = $this->getDoctrine()->getRepository(ToolTag::class);
            // TODO: pamastyti, gal galima panaudoti Repository klasę...?
            $tools = array();
            foreach ($tags as $tag) {
                $tagObj = $repo->findOneBy(array('tag' => $tag));
                if ($tagObj) {
                    $tools = array_merge($tools, $tagObj->getTools()->toArray());
                }
            }
            return $this->render('index.html.twig', array('tags' => $this->tags, 'tools' => array_unique($tools, SORT_REGULAR)));
        }
        return $this->redirectToRoute('index_page');
    }

    /**
     * @Route("/filter/{tag}", name="filter_by_single_tag", methods={"GET"})
     */
    public function filterBySingleTag($tag = null) {
        if ($tag) {
            $repo = $this->getDoctrine()->getRepository(ToolTag::class);
            $tagObj = $repo->findOneBy(array('tag' => $tag));
            $tools = array();
            if ($tagObj) {
                $tools = $tagObj->getTools();
            }
            return $this->render('index.html.twig', array('tags' => $this->tags, 'tools' => $tools));
        } else {
            return $this->redirectToRoute('index_page');
        }
    }

    /**
     * @Route("/search", name="search_tools")
     */
    public function search(Request $request) {
        if ($request->request->has('search_str')) {
            $searchStr = trim($request->request->get('search_str', ''));
            $toolRepo = $this->getDoctrine()->getRepository(Tool::class);
            // pirma patikrinam ar ieškoma pagal įrankio kodą
            if (is_numeric($searchStr) && (mb_strlen($searchStr) == 6)) {
                $tool = $toolRepo->findOneBy(array('code' => $searchStr));
                if ($tool) {
                    return $this->render('tool.html.twig',
                        array(
                            'tags' => $this->tags,
                            'tool' => $tool,
                            'search_str' => $searchStr
                        )
                    );
                }
            }

            // tada patikrinam ar yra toks tag'as ir jei taip gaunam susijusius įrankius
            $tagRepo = $this->getDoctrine()->getRepository(ToolTag::class);
            $tag = $tagRepo->findOneBy(array('tag' => $searchStr));
            if ($tag && ($tag->countTools() > 0)) {
                return $this->render('index.html.twig',
                    array(
                        'tags' => $this->tags,
                        'tools' => $tag->getTools(),
                        'search_str' => $searchStr
                    )
                );
            }

            $tools = $toolRepo->searchTools($searchStr);
            return $this->render('index.html.twig',
                array(
                    'tags' => $this->tags,
                    'tools' => $tools,
                    'search_str' => $searchStr
                )
            );
        }
        return $this->redirectToRoute('index_page');
    }
}
