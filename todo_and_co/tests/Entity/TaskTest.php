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

    public function testValidTask()
    {
        $task = (new Task())
            ->setTitle('Test task')
            ->setContent('This is the content')
        ;
        $this->getValidationErrors($task, 0);
    }

    public function testEmptyTitle()
    {
        $task = (new Task())
            ->setTitle('')
            ->setContent('This is the content')
        ;
        $this->getValidationErrors($task, 1);
    }

    public function testNullTitle()
    {
        $task = (new Task())
            ->setTitle(null)
            ->setContent('This is the content')
        ;
        $this->getValidationErrors($task, 2);
    }

    public function testInvalidContent()
    {
        $task = (new Task())
            ->setTitle('Test task')
            ->setContent('')
        ;
        $this->getValidationErrors($task, 1);
    }

    public function testNullContent()
    {
        $task = (new Task())
            ->setTitle('Test task')
            ->setContent(null)
        ;
        $this->getValidationErrors($task, 2);

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
