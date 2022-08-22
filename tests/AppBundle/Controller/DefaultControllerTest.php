<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = static::createClient(
            array(), array(
                'PHP_AUTH_USER' => 'teddy',
                'PHP_AUTH_PW'   => 'teddy',
            )
        );
    }

    /**
     * Test homepage with already logged in user
     *
     * @return void
     */
    public function testIndexUserIsLoggedIn()
    {
        $crawler = $this->client->request('GET', '/');

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
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertTrue(
            $client->getResponse()->isRedirect('http://localhost/login'),
            'response is a redirect to /login'
        );
    }
}
