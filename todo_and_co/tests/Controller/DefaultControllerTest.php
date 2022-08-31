<?php

namespace Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;
    private $databaseTool;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient();

        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $this->userRepository->findOneByUsername('test_admin');

        $this->client->loginUser($testUser);
        $this->client->followRedirects();
    }

    /**
     * Test homepage with already logged-in user
     *
     * @return void
     */
    public function testIndexUserIsLoggedIn()
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }

    /**
     * Test homepage without user logged in
     *
     * @return void
     */
    public function testIndexUserIsNotLoggedIn()
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        $client->request(Request::METHOD_GET, '/');

        $this->assertResponseRedirects('/login', 302);

        $client->followRedirect();

        $crawler = $client->getCrawler();

        $this->assertCount(1, $crawler->filter('#inputUsername'));
        $this->assertCount(1, $crawler->filter('#inputPassword'));
    }
}
