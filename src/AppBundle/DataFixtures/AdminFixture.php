<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixture extends Fixture {

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword($this->encoder->encodePassword($admin, 'test'));
        $admin->setRole('ROLE_SUPERADMIN');
        $manager->persist($admin);
        $manager->flush();
    }

}