<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GithubController extends AbstractController
{
    /**
     * @Route("/github", name="github")
     */
    public function index(): Response
    {
        return $this->render('github/index.html.twig', [
            'controller_name' => 'GithubController',
        ]);
    }
    /**
     * @Route("/github/webhook", name="githook")
     */
    public function webhook(Request $request) 
    {
        $rawContent = $request->getContent();
        $data = json_decode($rawContent, false);
        $signature = 'sha256=' . hash_hmac('sha256', json_encode(json_decode($rawContent), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES), $_ENV['WEBHOOK_SECRET']);
        $headerSignature = $request->headers->get('x-hub-signature-256');
        if($headerSignature == $signature){
            $myBranch = file_get_contents('../.git/HEAD');
            if(trim(substr($myBranch, 4)) == $data->ref){
                file_get_contents('cron:8192');
            }
        }
        return new Response();
    }
}
