<?php

namespace Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class UserControllerTest extends WebTestCase
{
    private $client = null;
    private $userRepository;
    private $entityManager;
    private $databaseTool;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $testUser = $this->userRepository->findOneByUsername('test_admin');

        $this->client->loginUser($testUser);
        $this->client->followRedirects();
    }

    /**
     * Test list users rendering
     */
    public function testListUsers()
    {
        $this->databaseTool->loadFixtures([
            UserFixtures::class,
        ]);

        $this->client->request(Request::METHOD_GET, '/users');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Create a user using the form /users/create
     */
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

        $this->assertStringContainsString(
            "Superbe ! L'utilisateur a bien été ajouté.",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Test edit a user feature
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

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            "Superbe ! L'utilisateur a bien été modifié",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Delete the userTest after tests execution
     */
    public static function tearDownAfterClass(): void
    {
        $userControllerTest = new UserControllerTest();
        $userControllerTest->setUp();

        $user = $userControllerTest->userRepository->findOneByUsername('UserTest_updated');

        $userControllerTest->entityManager->remove($user);
        $userControllerTest->entityManager->flush();
    }
}