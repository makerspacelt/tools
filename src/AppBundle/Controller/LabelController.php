<?php

namespace AppBundle\Controller;


use Picqer\Barcode\BarcodeGeneratorPNG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Makerspacelt\EsimLabelGernerator\Esim;

class LabelController extends Controller {

    const FONT_FILE = 'res/font/FreeMonoBold.ttf';
    const MARGIN = 10;
    const TITLE_LEN = 14;
    const MODEL_LEN = 24;

    /**
     * @Route("/label/{code}", name="tool_label_generator")
     */
    function generateLabel($code = null) {
        if ($code) {
            // reiktų patikrinti ar yra mums taip reikalingas font'as
            if (!file_exists(self::FONT_FILE)) {
//                generateErrorImg('Nerastas srifto failas:\n  \''.FONT_FILE.'\'');
                die('Font not found :(');
            } else {
                $title = 'testtest';
                $model = 'asdfg';
                $code = '45s5a4s5s4a';
                $url = 'http://9v.lt';
                $params = array(
                    'derp' => 'qqqqqq',
                    'herp' => 'wwwwwww',
                    'ish' => 'ssasasasa'
                );

                // apkarpome pavadinimą ir modelį, kad nebūtų per ilgas, ribojam iki 24 simbolių
                $title = mb_strtoupper(mb_substr($title, 0, self::TITLE_LEN - 1)) . (mb_strlen($title) > self::TITLE_LEN - 1 ? '~' : '');
                $model = mb_substr($model, 0, self::MODEL_LEN - 1) . (mb_strlen($model) > self::MODEL_LEN - 1 ? '~' : '');

                // pradedam generuoti etiketę
                // etiketės matmenys 52x40mm, printeris palaiko 8 pikselius per milimetrą,
                // tai 416x320 pilnai padengtas be marginų
                $baseImg = imagecreate(416, 320);

                $bg = imagecolorallocate($baseImg, 255, 255, 255); // background'as balta spalva
                $black = imagecolorallocate($baseImg, 0, 0, 0); // tekstas juoda

                // pavadinimas ir modelis
                imagettftext($baseImg, 35, 0, self::MARGIN, 40, $black, self::FONT_FILE, $title);
                imagettftext($baseImg, 20, 0, self::MARGIN, 70, $black, self::FONT_FILE, $model);
                imageline($baseImg, self::MARGIN, 120, imagesx($baseImg)-self::MARGIN, 120, $black);

                // įkeliam QR kodą
//                $qrSize = 170;
//                $qr = QRCode::getMinimumQRCode($url, QR_ERROR_CORRECT_LEVEL_L)->createImage(8, 4);
//                imagecopyresized(
//                    $baseImg, $qr,
//                    (imagesx($baseImg)-$qrSize)-MARGIN,
//                    (imagesy($baseImg)-$qrSize)-MARGIN-10,
//                    0, 0, $qrSize, $qrSize, imagesx($qr), imagesy($qr)
//                );

                // įkeliam barkodą
                // info dėl built-in fontų dydžio: https://docstore.mik.ua/orelly/webprog/pcook/ch15_06.htm
//                $generator = new BarcodeGeneratorPNG();
//                $barcode = imagecreatefromstring($generator->getBarcode($code, $generator::TYPE_INTERLEAVED_2_5_CHECKSUM));
//                imagecopy(
//                    $baseImg, $barcode,
//                    self::MARGIN, (imagesy($baseImg)-imagesy($barcode))-self::MARGIN-10,
//                    0, 0,
//                    imagesx($barcode), imagesy($barcode)
//                );
//                imagestring($baseImg, 5, self::MARGIN, (imagesy($baseImg)-15)-imagesy($barcode)-self::MARGIN-12, $code, $black);

                // pridedame įrankio parametrus
                $y = 160;
                $fontSize = 15;
                $lineLen = 15;
                if ($params && (count($params) > 0) && (!isset($params[0]))) {
                    foreach ($params as $key => $param) {
                        $line = $key.': '.$param;
                        imagettftext($baseImg, $fontSize, 0, self::MARGIN, $y, $black, self::FONT_FILE,
                            mb_substr($line, 0, $lineLen).(mb_strlen($line) > $lineLen ? '~' : '')
                        );
                        $y += $fontSize+8;
                    }
                } else {
                    imagettftext($baseImg, $fontSize, 0, self::MARGIN, $y, $black, self::FONT_FILE, 'Parametrų nėra');
                }

                ob_start();
                imagepng($baseImg);
                $pngImg = ob_get_contents();
                ob_end_clean();
                imagedestroy($baseImg);
                return new Response($pngImg, 200, array('Content-Type' => 'image/png'));

//                $e = new Esim();
//                $e->printLabel();
            }
        } else {
            return $this->redirectToRoute('index_page');
        }
    }

}