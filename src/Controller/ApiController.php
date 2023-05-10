<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ToolsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/api", name="api")
 */
class ApiController extends AbstractController
{
    private ToolsRepository $toolsRepository;

    public function __construct(ToolsRepository $toolsRepo)
    {
        $this->toolsRepository = $toolsRepo;
    }

    /**
     * @Route("/tools/{query}", name="api_tools")
     * @param string|null $query
     */
    public function tools($query = null): JsonResponse
    {
        return $this->json($this->toolsRepository->paginate($query));
    }
}
