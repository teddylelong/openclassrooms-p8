<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $user = (new User())
            ->setUsername('test_user')
            ->setPassword('0000')
            ->setEmail('user@localhost.com')
            ->setRoles(['ROLE_USER'])
        ;
        $manager->persist($user);

        $admin = (new User())
            ->setUsername('test_admin')
            ->setPassword('0000')
            ->setEmail('admin@localhost.com')
            ->setRoles(['ROLE_ADMIN'])
        ;
        $manager->persist($admin);

        $manager->flush();
    }
}