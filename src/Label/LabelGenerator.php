<?php

namespace App\Label;

use App\Entity\Tool;
use App\Label\Exception\LabelGeneratorException;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\Exceptions\BarcodeException;
use QR_Code\Types\QR_Url;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class LabelGenerator
{
    const FONT_FILE = __DIR__ . '/font/FreeMonoBold.ttf';
    const MARGIN = 10;
    const TITLE_LEN = 14;
    const MODEL_LEN = 24;

    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Tool $tool
     * @return resource
     * @throws LabelGeneratorException
     */
    public function generate(Tool $tool)
    {
        // TODO: tidy up and handle errors gracefully, not print them on sticker...
        // apkarpome pavadinimą ir modelį, kad nebūtų per ilgas, ribojam iki 24 simbolių
        $title = $this->trim($tool->getName(), self::TITLE_LEN);
        $model = $this->trim($tool->getModel(), self::MODEL_LEN);

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
            $this->router->generate(
                'tool_page',
                ['code' => $tool->getCode()],
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
            $barcode = imagecreatefromstring($generator->getBarcode(
                $tool->getCode(),
                BarcodeGeneratorPNG::TYPE_CODE_128,
                3,
                65
            ));
        } catch (BarcodeException $e) {
            throw new LabelGeneratorException("failed to generate barcode", 0, $e);
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

        return $baseImg;
    }

    private function trim(string $str, int $limit): string
    {
        if (mb_strlen($str) > $limit) {
            $str = mb_substr($str, 0, $limit - 1) . '~';
        }

        return mb_strtoupper($str);
    }
}
