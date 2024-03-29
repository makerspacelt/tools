<?php

namespace App\Controller;

use PhpParser\Node\Stmt\Catch_;
use SebastianBergmann\Environment\Console;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

class GithubController extends AbstractController
{
    /**
     * @Route("/github", name="github")
     */
    public function index(): Response
    {
        return $this->render(
            'github/index.html.twig',
            [
                'controller_name' => 'GithubController',
            ]
        );
    }

    /**
     * @Route("/github/webhook", name="githook")
     */
    public function webhook(Request $request)
    {
        try {
            $rawContent = $request->getContent();
            $data = json_decode($rawContent, false);
            $signature = 'sha256=' . hash_hmac('sha256', json_encode(json_decode($rawContent), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $_ENV['WEBHOOK_SECRET']);
            $headerSignature = $request->headers->get('x-hub-signature-256');
            if ($headerSignature == $signature) {
                $myBranch = file_get_contents('../.git/HEAD');
                if(trim(substr($myBranch, 4)) == $data->ref) {
                    file_put_contents("../actions/purge", date("d-m-y H:i:s"));
                    echo "Calling cron for suicide";
                    file_get_contents('http://cron:8192');
                }else{
                    echo "Push was to different branch";
                }
            }else{
                echo "Header signature does not match";
            }

        }
        catch(Exception $ex){
            throw $ex;
        }finally{
            return new Response();
        }
    }
}
