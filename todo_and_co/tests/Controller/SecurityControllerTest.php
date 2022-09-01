<?php

namespace Tests\Controller;

use App\DataFixtures\UserFixtures;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SecurityControllerTest extends WebTestCase
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
    }

    /**
     * Test the login page rendering
     *
     * @return void
     */
    public function testLoginUserIsNotLoggedIn()
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $crawler = $this->client->request(Request::METHOD_GET, '/login');

         $this->assertTrue($this->client->getResponse()->isSuccessful());
         $this->assertCount(1, $crawler->filter('#inputUsername'));
         $this->assertCount(1, $crawler->filter('#inputPassword'));
    }

    /**
     * Test login form
     *
     * @return void
     */
    public function testLoginForm()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/login');

        $form = $crawler->selectButton('Se connecter')->form();
        $this->client->submit($form, array(
           'username' => 'test_user',
           'password' => '0000',
        ));
        $this->client->followRedirect();

        $crawler = $this->client->getCrawler();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }
}