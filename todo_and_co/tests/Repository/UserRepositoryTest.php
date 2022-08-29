<?php

namespace Tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private $userRepository;
    private $databaseTool;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testUserRepository()
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $users = $this->userRepository->count([]);
        $this->assertEquals(2, $users);
    }
}