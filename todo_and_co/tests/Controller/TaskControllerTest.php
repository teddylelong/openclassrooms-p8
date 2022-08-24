<?php

namespace Tests\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends WebTestCase
{
    private $client = null;

    private $taskRepository;

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

        $this->taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
    }

    /**
     * Test tasks list rendering
     *
     * @return void
     */
    public function testListTasks()
    {
        $this->client->request(Request::METHOD_GET, '/tasks');
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * Test create a task form
     *
     * @return void
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

        $this->assertContains(
            "Superbe ! La tâche a été bien été ajoutée.",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Test edit a task form feature
     *
     * @return void
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
        $this->assertContains(
            "Superbe ! La tâche a bien été modifiée.",
            $crawler->filter('div.alert.alert-success')->text(null, false)
        );
    }

    /**
     * Test toggle a task feature
     *
     * @return void
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
     *
     * @return void
     */
    public function testRemoveTask()
    {
        $task = $this->taskRepository->findOneByTitle('Updated task');
        $taskId = $task->getId();

        $this->client->request(Request::METHOD_GET, "/tasks/$taskId/delete");

        $this->client->followRedirect();

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertNull($this->taskRepository->find($taskId));
    }
}