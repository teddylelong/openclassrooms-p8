<?php

namespace Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends WebTestCase
{
    private $client = null;

    private $taskRepository;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('test_user');

        $this->client->loginUser($testUser);

        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    /**
     * Test tasks list rendering
     */
    public function testListTasks()
    {
        $this->client->request(Request::METHOD_GET, '/tasks');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Test create a task form
     */
    public function testCreateTask()
    {
        $crawler = $this->client->request(Request::METHOD_GET, '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $this->client->submit($form, array(
            'task[title]'   => 'Test task title',
            'task[content]' => 'This is the test task content. Remove me :)',
        ));

        $this->client->followRedirect();

        $crawler = $this->client->getCrawler();

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->assertStringContainsString(
            "Superbe ! La tâche a été bien été ajoutée.",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Test edit a task using form /task/create
     */
    public function testEditTask()
    {
        $task = $this->taskRepository->findOneByTitle('Test task title');
        $taskId = $task->getId();

        $crawler = $this->client->request(Request::METHOD_GET, "/tasks/$taskId/edit");
        $form = $crawler->selectButton('Modifier')->form();
        $this->client->submit($form, array(
            'task[title]' => 'Updated task',
            'task[content]' => 'This is the test task updated content. Remove me :)',
        ));
        $this->client->followRedirect();

        $crawler = $this->client->getCrawler();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            "Superbe ! La tâche a bien été modifiée.",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Test toggle a task feature
     */
    public function testToggleTask()
    {
        $task = $this->taskRepository->findOneByTitle('Updated task');
        $taskId = $task->getId();

        $this->client->request(Request::METHOD_GET, "/tasks/$taskId/toggle");

        $this->client->followRedirect();

        $this->assertEquals(1, $task->isDone());
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Test remove a task feature
     */
    public function testDeleteTask()
    {
        $task = $this->taskRepository->findOneByTitle('Updated task');
        $taskId = $task->getId();

        $this->client->request(Request::METHOD_GET, "/tasks/$taskId/delete");

        $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertNull($this->taskRepository->find($taskId));
    }
}
