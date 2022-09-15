<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class TaskFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 14; $i++) {
            $task = (new Task())
                ->setTitle("Task #$i")
                ->setContent("This is the content of task #$i")
            ;

            if ($i < 5) {
                $task->setUser($this->getReference('user'));
            } elseif( $i < 9) {
                $task->setUser($this->getReference('admin'));
            } else {
                $task->setUser(null);
            }

            $manager->persist($task);
        }
        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }
}