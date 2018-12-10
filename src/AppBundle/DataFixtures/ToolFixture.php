<?php
/**
 * Created by PhpStorm.
 * User: Kulverstukas
 * Date: 2018-09-09
 * Time: 19:41
 */

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Tool;
use AppBundle\Entity\ToolLog;
use AppBundle\Entity\ToolParameter;
use AppBundle\Entity\ToolTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ToolFixture extends Fixture {

    public function load(ObjectManager $manager) {
        for ($j = 0; $j < 5; $j++) {
            $tool = new Tool();
            $tool->setName(substr(md5(microtime()), rand(0, 26), 6));
            $tool->setModel(substr(md5(microtime()), rand(0, 26), 4));
            $tool->setCode(rand(100000, 999999));
            $tool->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
            $tool->setShopLinks("http://9v.lt\nhttps://google.com\nir senukai ar kažkur");
            $tool->setOriginalPrice(rand(1, 100));
            $tool->setAcquisitionDate('2018-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT));

            $tags = array('drill', 'drillbit', 'zeimeris', 'druzba', 'pjuklas', 'diskas', 'slifarke', 'oblius', 'atsuktuvas-kryzminis', 'atsuktuvas-minusas', 'rulete');
            // TODO: kažkaip fetch'inti iš DB kad užtikrinti unikalumą
            for ($i = 0; $i < 4; $i++) {
                $tag = new ToolTag();
                $tag->setTag($tags[rand(0, count($tags) - 1)]);
                $tool->addTag($tag);
                $manager->persist($tag);
            }

            for ($i = 0; $i < 3; $i++) {
                $param = new ToolParameter();
                $param->setName(substr(md5(microtime()), rand(0, 26), 7));
                $param->setValue(substr(md5(microtime()), rand(0, 26), 8));
                $tool->addParam($param);
                $manager->persist($param);
            }

            $logArr = array('Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.');
            for ($i = 0; $i < 2; $i++) {
                $log = new ToolLog();
                $log->setLog($logArr[$i]);
                $tool->addLog($log);
                $manager->persist($log);
            }

            $manager->persist($tool);
        }

        $manager->flush();
    }
}