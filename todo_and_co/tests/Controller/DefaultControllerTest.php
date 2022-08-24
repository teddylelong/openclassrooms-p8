<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    /**
     * @return void
     */
    public function setUp()
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient([], [
                'PHP_AUTH_USER' => 'test_user',
                'PHP_AUTH_PW'   => 'test_user',
            ]
        );
    }

    /**
     * Test homepage with already logged in user
     *
     * @return void
     */
    public function testIndexUserIsLoggedIn()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/');

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );

        // var_dump($this->client->getResponse()->getContent());
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

        $this->assertTrue(
            $client->getResponse()->isRedirect('http://localhost/login'),
            'response is a redirect to /login'
        );

        $client->followRedirect();

        $crawler = $client->getCrawler();

        $this->assertCount(1, $crawler->filter('#username'));
        $this->assertCount(1, $crawler->filter('#password'));
    }
}
