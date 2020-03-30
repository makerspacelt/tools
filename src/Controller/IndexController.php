<?php

namespace App\Controller;

use App\Repository\TagsRepository;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /** @var ToolsRepository */
    private $toolsRepo;

    /** @var TagsRepository */
    private $tagsRepo;

    function __construct(ToolsRepository $toolsRepo, TagsRepository $tagsRepo)
    {
        $this->toolsRepo = $toolsRepo;
        $this->tagsRepo = $tagsRepo;
    }

    /**
     * @Route("/", name="index_page")
     */
    public function index(): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'tools' => $this->toolsRepo->findAll(),
            ]
        );
    }

    /**
     * @Route("/filter", name="filter_by_tags", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function filterByTags(Request $request): Response
    {
        if (!$request->request->has('tags')) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'index.html.twig',
            [
                'tools' => $this->toolsRepo->findByTags($request->request->get('tags', [])),
            ]
        );
    }

    /**
     * @Route("/filter/{tag}", name="filter_by_single_tag", methods={"GET"})
     * @param string|null $tag
     * @return Response
     */
    public function filterBySingleTag($tag = null): Response
    {
        if (is_null($tag)) {
            return $this->redirectToRoute('index_page');
        }

        return $this->render(
            'index.html.twig',
            [
                'tools' => $this->toolsRepo->findByTags([$tag]),
            ]
        );
    }

    /**
     * @Route("/search", name="search_tools")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        if (!$request->request->has('search_str')) {
            return $this->redirectToRoute('index_page');
        }

        $searchStr = trim($request->request->get('search_str', ''));
        // pirma patikrinam ar ieškoma pagal įrankio kodą
        if (is_numeric($searchStr) && (mb_strlen($searchStr) == 6)) {
            $tool = $this->toolsRepo->findOneBy(['code' => $searchStr]);
            if ($tool) {
                return $this->render('tool.html.twig',
                    [
                        'tool'       => $tool,
                        'search_str' => $searchStr,
                    ]
                );
            }
        }

        // tada patikrinam ar yra toks tag'as ir jei taip gaunam susijusius įrankius
        $tag = $this->tagsRepo->findOneBy(['tag' => $searchStr]);
        if ($tag && ($tag->countTools() > 0)) {
            return $this->render('index.html.twig',
                [
                    'tools'      => $tag->getTools(),
                    'search_str' => $searchStr,
                ]
            );
        }

        return $this->render('index.html.twig',
            [
                'tools'      => $this->toolsRepo->searchTools($searchStr),
                'search_str' => $searchStr,
            ]
        );
    }
}
