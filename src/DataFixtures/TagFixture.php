<?php

namespace App\DataFixtures;

use App\Entity\ToolTag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = [
            'drill',
            'drillbit',
            'zeimeris',
            'druzba',
            'pjuklas',
            'diskas',
            'slifarke',
            'oblius',
            'atsuktuvas-kryzminis',
            'atsuktuvas-minusas',
            'rulete',
        ];

        foreach ($tags as $tag) {
            $toolTag = new ToolTag();
            $toolTag->setTag($tag);
            $manager->persist($toolTag);
        }
        $manager->flush();
    }

}
