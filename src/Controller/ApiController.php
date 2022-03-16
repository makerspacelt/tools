<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ToolsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api", name="api")
 */
class ApiController extends AbstractController
{
    private ToolsRepository $toolsRepository;
    private LoggerInterface $logger;

    public function __construct(ToolsRepository $toolsRepo, LoggerInterface $logger){
        $this->toolsRepository = $toolsRepo;
        $this->logger = $logger;
    }
    /**
     * @Route("/", name="api_index")
     */
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
    /**
     * @Route("/tools/{query}", name="api_tools")
     * @param string|null $query
     */
    public function tools($query = null) : JsonResponse{
        return $this->json($this->toolsRepository->paginate($query)); // new Response(json_encode($this->toolsRepository->paginate("")));
    }
}
