<?php

namespace Tests\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

class TaskTest extends KernelTestCase
{
    private $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        static::bootKernel();
        $container = self::$kernel->getContainer()->get('test.service_container');
        $this->validator = $container->get('validator');
    }

    /**
     * @return Task
     */
    public function getEntity(): Task
    {
        return (new Task())
            ->setTitle('Test task')
            ->setContent('This is the content');
    }

    public function testValidTask()
    {
        $this->getValidationErrors($this->getEntity(), 0);
    }

    public function testEmptyTitle()
    {
        $this->getValidationErrors($this->getEntity()->setTitle(''), 1);
    }

    public function testNullTitle()
    {
        $this->getValidationErrors($this->getEntity()->setTitle(null), 2);
    }

    public function testEmptyContent()
    {
        $this->getValidationErrors($this->getEntity()->setContent(''), 1);
    }

    public function testNullContent()
    {
        $this->getValidationErrors($this->getEntity()->setContent(null), 2);

    }

    /**
     * @param Task $task
     * @param int $numberOfExpectedErrors
     * @return ConstraintViolationList
     */
    private function getValidationErrors(Task $task, int $numberOfExpectedErrors): ConstraintViolationList
    {
        $errors = $this->validator->validate($task);
        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;
    }
}
