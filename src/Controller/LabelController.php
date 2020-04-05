<?php

namespace App\Controller;

use App\Label\Exception\LabelGeneratorException;
use App\Label\LabelGenerator;
use App\Repository\ToolsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Makerspacelt\EsimLabelGernerator\EsimPrint;

class LabelController extends AbstractController
{
    /** @var LabelGenerator */
    private $generator;

    /** @var ToolsRepository */
    private $toolsRepo;

    public function __construct(LabelGenerator $generator, ToolsRepository $toolsRepo)
    {
        $this->generator = $generator;
        $this->toolsRepo = $toolsRepo;
    }

    /**
     * @Route("/label/{code}", name="tool_label_generator")
     * @param string $code
     * @return Response
     */
    public function generateLabel(string $code)
    {
        $tool = $this->toolsRepo->findOneBy(['code' => $code]);
        if (!$tool) {
            return new Response(sprintf("tools by code '%s' not found", $code), 404);
        }

        try {
            $image = $this->generator->generate($tool);
        } catch (LabelGeneratorException $e) {
            return new Response($this->combineExceptionMessage($e), 500);
        }

        ob_start();
        imagepng($image);
        $pngImage = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);

        return new Response($pngImage, 200, ['Content-Type' => 'image/png']);
    }

    /**
     * @Route("/print/{code}", name="tool_label_printer")
     */
    public function printLabel(string $code)
    {
        $tool = $this->toolsRepo->findOneBy(['code' => $code]);
        if (!$tool) {
            return new JsonResponse([
                'response'  => false,
                'error_msg' => sprintf("tools by code '%s' not found", $code),
            ]);
        }

        try {
            $image = $this->generator->generate($tool);
        } catch (LabelGeneratorException $e) {
            return new JsonResponse([
                'response'  => false,
                'error_msg' => $this->combineExceptionMessage($e),
            ]);
        }

        $esimPrint = new EsimPrint();
        $labelData = $esimPrint->printGd($image);

        // TODO: perkelti hostname'ą ir port'ą į admin panelės overview/config langą
        try {
            $fp = fsockopen('print-label.lan', 80, $errno, $errstr, 5);
        } catch (\Exception $e) {
            return new JsonResponse([
                'response'  => false,
                'error_msg' => $this->combineExceptionMessage($e),
            ]);
        }
        if (!$fp) {
            return new JsonResponse([
                'response'  => false,
                'error_msg' => sprintf("ERROR: %d - %s", $errno, $errstr),
            ]);
        }

        fwrite($fp, $labelData);
        fread($fp, 26);
        fclose($fp);

        return new JsonResponse(['response' => true]);
    }

    private function combineExceptionMessage(\Exception $e): string
    {
        $messages = [];
        while (!is_null($e)) {
            $messages[] = $e->getMessage();
            $e = $e->getPrevious();
        }

        return implode("; ", $messages);
    }
}
