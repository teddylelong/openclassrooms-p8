<?php

namespace Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends WebTestCase
{
    private $client = null;
    private $userRepository;
    private $entityManager;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->client = static::createClient([], [
                'PHP_AUTH_USER' => 'test_user',
                'PHP_AUTH_PW'   => 'test_user',
            ]
        );
        $this->client->followRedirects();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    /**
     * Test list users rendering
     *
     * @return void
     */
    public function testListUsers()
    {
        $this->client->request(Request::METHOD_GET, '/users');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testCreateUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $this->client->submit($form, array(
            'user[username]'         => 'UserTest',
            'user[password][first]'  => 'Us3rT3$T',
            'user[password][second]' => 'Us3rT3$T',
            'user[email]'            => 'user@example.com',
        ));

        $crawler = $this->client->getCrawler();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertContains(
            "Superbe ! L'utilisateur a bien été ajouté.",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Test edit a user feature
     *
     * @return void
     */
    public function testEditUser()
    {
        $user = $this->userRepository->findOneByUsername('UserTest');
        $userId = $user->getId();

        $crawler = $this->client->request(Request::METHOD_GET, "/users/$userId/edit");
        $form = $crawler->selectButton('Modifier')->form();
        $this->client->submit($form, array(
            'user[username]'         => 'UserTest_updated',
            'user[password][first]'  => 'Us3rT3$T_updated',
            'user[password][second]' => 'Us3rT3$T_updated',
            'user[email]'            => 'user_updated@example.com',
        ));

        $crawler = $this->client->getCrawler();

        // $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains(
            "Superbe ! L'utilisateur a bien été modifié",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Delete the userTest after tests execution
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        $userControllerTest = new UserControllerTest();
        $userControllerTest->setUp();

        $user = $userControllerTest->userRepository->findOneByUsername('UserTest_updated');

        $userControllerTest->entityManager->remove($user);
        $userControllerTest->entityManager->flush();
    }
}