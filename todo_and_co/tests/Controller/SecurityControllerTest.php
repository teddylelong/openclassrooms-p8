<?php

namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SecurityControllerTest extends WebTestCase
{
    private $client = null;

    /**
     * @return void
     */
    public function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client = static::createClient();
    }

    /**
     * Test the login page rendering
     *
     * @return void
     */
    public function testLoginUserIsNotLoggedIn()
    {
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
           'password' => 'test_user',
        ));
        $this->client->followRedirect();

        $crawler = $this->client->getCrawler();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !",
            $crawler->filter('h1')->text()
        );
    }

    /**
     * @return void
     */
//    public function testLogout()
//    {
//        $client = static::createClient(
//            array(), array()
//        );
//
//        $crawler = $client->request(Request::METHOD_GET, '/logout');
//
//        $client->followRedirect();
//
//        $client->reload();
//
//        var_dump($crawler->getUri());
//
//        $this->assertFalse($client->getRequest()->getUser());
//        $this->assertCount(1, $crawler->filter('#username'));
//        $this->assertCount(1, $crawler->filter('#password'));
//
//        echo $client->getResponse()->getContent();
//    }
}