<?php

namespace Tests\Repository;

use App\DataFixtures\TaskFixtures;
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

    public function testTaskRepository()
    {
        $this->databaseTool->loadFixtures([
            TaskFixtures::class,
        ]);

        $tasks = $this->taskRepository->count([]);
        $this->assertEquals(10,$tasks);
    }
}