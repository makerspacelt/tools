<?php

namespace App\Controller;

use App\Entity\Tool;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\Exceptions\BarcodeException;
use QR_Code\Types\QR_Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Makerspacelt\EsimLabelGernerator\EsimPrint;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LabelController extends AbstractController
{
    const FONT_FILE = 'res/font/FreeMonoBold.ttf';
    const MARGIN = 10;
    const TITLE_LEN = 14;
    const MODEL_LEN = 24;

    private function generateErrorLabel($errorText, $rtn = false)
    {
        $baseImg = imagecreate(416, 320);
        imagecolorallocate($baseImg, 255, 255, 255);
        $black = imagecolorallocate($baseImg, 0, 0, 0);
        $yCoord = 50;
        imagestring($baseImg, 5, 50, $yCoord, 'Nu... negerai!', $black);
        $strArr = explode('\n', $errorText);
        foreach ($strArr as $str) {
            $yCoord += 20;
            imagestring($baseImg, 5, 50, $yCoord, $str, $black);
        }
        if ($rtn) {
            return $baseImg;
        } else {
            ob_start();
            imagepng($baseImg);
            $pngImg = ob_get_contents();
            ob_end_clean();
            imagedestroy($baseImg);
            return new Response($pngImg, 200, ['Content-Type' => 'image/png']);
        }
    }

    /**
     * @Route("/label/{code}", name="tool_label_generator")
     */
    public function generateLabel($code = null, $rtn = false)
    {
        if ($code) {
            // patikriname pirma ar yra toks įrankis pagal nurodytą kodą
            $repo = $this->getDoctrine()->getRepository(Tool::class);
            $tool = $repo->findOneBy(['code' => $code]);
            if (!$tool) {
                return self::generateErrorLabel('Nerastas irankis pagal nurodyta koda:\n  \'' . $code . '\'', $rtn);
            }
            // reiktų patikrinti ar yra mums taip reikalingas font'as
            if (!file_exists(self::FONT_FILE)) {
                return self::generateErrorLabel('Nerastas srifto failas:\n  \'' . self::FONT_FILE . '\'', $rtn);
            } else {
                // apkarpome pavadinimą ir modelį, kad nebūtų per ilgas, ribojam iki 24 simbolių
                $title = mb_strtoupper(mb_substr($tool->getName(), 0,
                        self::TITLE_LEN - 1)) . (mb_strlen($tool->getName()) > self::TITLE_LEN - 1 ? '~' : '');
                $model = mb_substr($tool->getModel(), 0,
                        self::MODEL_LEN - 1) . (mb_strlen($tool->getModel()) > self::MODEL_LEN - 1 ? '~' : '');

                // pradedam generuoti etiketę
                // etiketės matmenys 52x40mm, printeris palaiko 8 pikselius per milimetrą,
                // tai 416x320 pilnai padengtas be marginų
                $baseImg = imagecreate(416, 320);

                imagecolorallocate($baseImg, 255, 255, 255); // background'as balta spalva
                $black = imagecolorallocate($baseImg, 0, 0, 0); // tekstas juoda

                // pavadinimas ir modelis
                imagettftext($baseImg, 35, 0, self::MARGIN, 40, $black, self::FONT_FILE, $title);
                imagettftext($baseImg, 20, 0, self::MARGIN, 70, $black, self::FONT_FILE, $model);
                imageline($baseImg, self::MARGIN, 120, imagesx($baseImg) - self::MARGIN, 120, $black);

                // įkeliam QR kodą
                $qrUrl = new QR_Url(
                    $this->generateUrl(
                        'tool_page',
                        ['code' => $code],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    )
                );
                $qrUrl->setMargin(0);
                ob_start();
                $qrUrl->png();
                $qrCode = ob_get_contents();
                ob_end_clean();
                $qrCode = imagecreatefromstring($qrCode);
                $qrSize = 170;
                imagecopyresized(
                    $baseImg, $qrCode,
                    (imagesx($baseImg) - $qrSize) - self::MARGIN,
                    (imagesy($baseImg) - $qrSize) - self::MARGIN - 10,
                    0, 0, $qrSize, $qrSize, imagesx($qrCode), imagesy($qrCode)
                );

                // įkeliam barkodą
                // info dėl built-in fontų dydžio: https://docstore.mik.ua/orelly/webprog/pcook/ch15_06.htm
                $generator = new BarcodeGeneratorPNG();
                try {
                    $barcode = imagecreatefromstring($generator->getBarcode($tool->getCode(),
                        $generator::TYPE_INTERLEAVED_2_5_CHECKSUM));
                } catch (BarcodeException $e) {
                    return self::generateErrorLabel('Klaida generuojant barkodą:\n  ' . $e->getMessage(), $rtn);
                }
                imagecopy(
                    $baseImg, $barcode,
                    self::MARGIN, (imagesy($baseImg) - imagesy($barcode)) - self::MARGIN - 10,
                    0, 0,
                    imagesx($barcode), imagesy($barcode)
                );
                imagestring($baseImg, 5, self::MARGIN, (imagesy($baseImg) - 15) - imagesy($barcode) - self::MARGIN - 12,
                    $tool->getCode(), $black);

                // pridedame įrankio parametrus
                $y = 160;
                $fontSize = 15;
                $lineLen = 15;
                $toolParams = $tool->getParams();
                if (count($toolParams) > 0) {
                    foreach ($toolParams as $param) {
                        $line = $param->getName() . ': ' . $param->getValue();
                        imagettftext($baseImg, $fontSize, 0, self::MARGIN, $y, $black, self::FONT_FILE,
                            mb_substr($line, 0, $lineLen) . (mb_strlen($line) > $lineLen ? '~' : '')
                        );
                        $y += $fontSize + 8;
                    }
                } else {
                    imagettftext($baseImg, $fontSize, 0, self::MARGIN, $y, $black, self::FONT_FILE, 'Parametrų nėra');
                }

                if ($rtn) {
                    return $baseImg;
                } else {
                    ob_start();
                    imagepng($baseImg);
                    $pngImg = ob_get_contents();
                    ob_end_clean();
                    imagedestroy($baseImg);
                    return new Response($pngImg, 200, ['Content-Type' => 'image/png']);
                }
            }
        } else {
            return $this->redirectToRoute('index_page');
        }
    }

    /**
     * @Route("/print", name="tool_label_printer")
     */
    public function printLabel(Request $request)
    {
        if ($request->request->has('tool_code')) {
            $resp = ['response' => false];
            $tCode = $request->request->get('tool_code', '0');
            $esimPrint = new EsimPrint();
            $labelData = $esimPrint->printGd($this->generateLabel($tCode, true));
            // TODO: perkelti hostname'ą ir port'ą į admin panelės overview/config langą
            $fp = fsockopen('print-label.lan', 80, $errno, $errstr, 5);

            if (!$fp) {
                $resp['error_msg'] = "ERROR: $errno - $errstr";
            } else {
                fwrite($fp, $labelData);
                fread($fp, 26);
                fclose($fp);
                $resp['response'] = true;
            }
            return new Response(json_encode($resp));
        }
        return $this->redirectToRoute('index_page');
    }

}