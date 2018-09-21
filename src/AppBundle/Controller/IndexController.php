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
     * @Route("/search", name="filter_by_tags", methods={"POST"})
     */
    public function filterByTags(Request $request) {

    }

    /**
     * @Route("/search", name="filter_by_single_tag", methods={"GET"})
     */
    public function filterBySingleTag(Request $request) {
        if ($request->query->has('tag')) {
            $tags = $request->query->get('tag', '');
            $repo = $this->getDoctrine()->getRepository(ToolTag::class);
            $tagObj = $repo->findOneBy(array('tag' => $tags));
            $tools = array();
            if ($tagObj) {
                $tools = $tagObj->getTools();
            }
            return $this->render('index.html.twig', array('tags' => $this->tags, 'tools' => $tools));
        }
        return $this->redirectToRoute('index_page');
    }
}
