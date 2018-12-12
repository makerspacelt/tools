<?php

namespace AppBundle\DataFixtures;


use AppBundle\Entity\ToolTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TagFixture extends Fixture {

    public function load(ObjectManager $manager) {
        $tags = array('drill', 'drillbit', 'zeimeris', 'druzba', 'pjuklas', 'diskas', 'slifarke', 'oblius',
            'atsuktuvas-kryzminis', 'atsuktuvas-minusas', 'rulete');

        foreach ($tags as $tag) {
            $toolTag = new ToolTag();
            $toolTag->setTag($tag);
            $manager->persist($toolTag);
        }
        $manager->flush();
    }

}