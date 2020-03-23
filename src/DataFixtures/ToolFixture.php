<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use App\Entity\ToolLog;
use App\Entity\ToolParameter;
use App\Entity\ToolTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

class ToolFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $tags = $manager->getRepository(ToolTag::class)->findAll();
        for ($j = 0; $j < 5; $j++) {
            $tool = new Tool();
            $tool->setName(substr(md5(microtime()), rand(0, 26), 6));
            $tool->setModel(substr(md5(microtime()), rand(0, 26), 4));
            $tool->setCode(rand(100000, 999999));
            $tool->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
            $tool->setShopLinks("http://9v.lt\nhttps://google.com\nir senukai ar kažkur");
            $tool->setOriginalPrice(rand(1, 100));
            try {
                $tool->setAcquisitionDate(new \DateTime('2018-' . str_pad(rand(1, 12), 2, '0',
                        STR_PAD_LEFT) . '-' . str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT)));
            } catch (Exception $e) {
                continue;
            }

            $usedTags = [];
            for ($i = 0; $i < rand(3, 8); $i++) {
                do {
                    $tag = $tags[rand(0, count($tags) - 1)];
                } while (in_array($tag->getTag(), $usedTags));
                $usedTags[] = $tag->getTag();
                $tool->addTag($tag);
            }

            for ($i = 0; $i < 3; $i++) {
                $param = new ToolParameter();
                $param->setName(substr(md5(microtime()), rand(0, 26), 7));
                $param->setValue(substr(md5(microtime()), rand(0, 26), 8));
                $tool->addParam($param);
                $manager->persist($param);
            }

            $logArr = [
                'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.',
                'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.',
            ];
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

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return [TagFixture::class];
    }
}
