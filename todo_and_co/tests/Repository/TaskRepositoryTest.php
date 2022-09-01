<?php

namespace Tests\Repository;

use App\DataFixtures\TaskFixtures;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    private $taskRepository;
    private $databaseTool;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    /**
     * Test task repository
     */
    public function testTaskRepository()
    {
        $this->databaseTool->loadFixtures([
            TaskFixtures::class,
        ]);

        $tasks = $this->taskRepository->count([]);
        $this->assertEquals(10,$tasks);
    }

    /**
     * Test add() and delete() methods
     */
    public function testAddAndDeleteTask()
    {
        $task = (new Task())
            ->setTitle('test-delete-me')
            ->setContent('test-content')
        ;
        $this->taskRepository->add($task);

        $test = $this->taskRepository->findOneBy([
            'title' => 'test-delete-me'
        ]);

        $this->assertSame('test-delete-me', $test->getTitle());

        $task = $this->taskRepository->findOneBy([
            'title' => 'test-delete-me'
        ]);
        $this->taskRepository->remove($task);

        $test = $this->taskRepository->findOneBy([
            'title' => 'test-delete-me'
        ]);

        $this->assertNull($test);
    }
}
