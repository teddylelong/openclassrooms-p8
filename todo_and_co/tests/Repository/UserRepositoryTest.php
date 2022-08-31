<?php

namespace Tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Entity\User;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRepositoryTest extends KernelTestCase
{
    private $userRepository;
    private $databaseTool;
    private $hasher;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
    }

    public function testUserRepository()
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $users = $this->userRepository->count([]);
        $this->assertEquals(2, $users);
    }

    public function testAddAndDeleteUser()
    {
        $user = (new User())
            ->setUsername('test-delete-me')
            ->setEmail('test@example.com')
        ;
        $user->setPassword($this->hasher->hashPassword($user, '0000'));

        $this->userRepository->add($user, true);

        $test = $this->userRepository->findOneBy([
            'username' => 'test-delete-me'
        ]);

        $this->assertSame('test-delete-me', $test->getUsername());

        $user = $this->userRepository->findOneBy([
            'username' => 'test-delete-me'
        ]);
        $this->userRepository->remove($user, true);

        $test = $this->userRepository->findOneBy([
            'username' => 'test-delete-me'
        ]);

        $this->assertNull($test);
    }

    public function testUpgradePassword()
    {
        $user = $this->userRepository->findOneByUsername('test_user');
        $hash = $this->hasher->hashPassword($user, '1234');

        $this->userRepository->upgradePassword($user, $hash);

        $user = $this->userRepository->findOneByUsername('test_user');
        $this->assertSame($hash, $user->getPassword());
    }
}