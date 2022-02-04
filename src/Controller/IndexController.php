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
    private ToolsRepository $toolsRepo;
    private TagsRepository $tagsRepo;

    public function __construct(ToolsRepository $toolsRepo, TagsRepository $tagsRepo)
    {
        $this->toolsRepo = $toolsRepo;
        $this->tagsRepo = $tagsRepo;
    }

    /**
     * @Route("/", name="index_page")
     */
    public function index(Request $request): Response
    {
        return $this->render(
            'index.html.twig',
            [
                'tools'  => $this->toolsRepo->findAll(),
                'locale' => $request->getLocale(),
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
    public function filterBySingleTag(?string $tag = null): Response
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
     * @Route("/changelang/{locale}", name="change_lang", methods={"GET"})
     * @param Request     $request
     * @param string|null $locale
     * @return Response
     */
    public function lang(Request $request, ?string $locale = null): Response
    {
        $referer = $request->headers->get('referer');
        if (!$locale) {
            return $this->redirect($referer);
        }

        $request->getSession()->set('_locale', $locale);
        return $this->redirect($referer);
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

        $code = trim($request->request->get('search_str', ''));
        // check if search string is more or equal to 6 and if its code
        $endcode = substr($code, -6, 6);
        $startcode = substr($code, 0, 6);
        if (mb_strlen($code) >= 6 && (is_numeric($endcode) || is_numeric($startcode))) {
            $tool = $this->toolsRepo->findOneBy(['code' => $startcode]);
            if ($tool) {
                return $this->render('tool.html.twig', [
                    'tool' => $tool,
                ]);
            }
            $tool = $this->toolsRepo->findOneBy(['code' => $endcode]);
            if ($tool) {
                return $this->render('tool.html.twig', [
                    'tool' => $tool,
                ]);
            }
        }

        // tada patikrinam ar yra toks tag'as ir jei taip gaunam susijusius įrankius
        $tag = $this->tagsRepo->findOneBy(['tag' => $code]);
        if ($tag && ($tag->countTools() > 0)) {
            return $this->render(
                'index.html.twig',
                [
                    'tools'      => $tag->getTools(),
                    'search_str' => $code,
                ]
            );
        }

        return $this->render(
            'index.html.twig',
            [
                'tools'      => $this->toolsRepo->searchTools($code),
                'search_str' => $code,
            ]
        );
    }
}
