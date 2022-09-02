<?php

namespace Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationList;

class UserTest extends KernelTestCase
{
    private $validator;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->validator = self::getContainer()->get('validator');
    }

    /**
     * Get a valid entity
     *
     * @return User
     */
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('TestUser')
            ->setPassword('p@ssw0rd')
            ->setEmail('email@example.net')
            ->setRoles(['ROLE_USER'])
        ;
    }

    /**
     * Test a valid user Entity
     */
    public function testValidUser()
    {
        $this->getValidationErrors($this->getEntity());
    }

    /**
     * Test with empty username
     */
    public function testUsernameEmpty()
    {
        $this->getValidationErrors($this->getEntity()->setUsername(''), 2);
    }

    /**
     * Test with null username
     */
    public function testUsernameNull()
    {
        $this->expectException(\TypeError::class);
        $this->getEntity()->setUsername(null);
    }

    /**
     * Test with a username bigger than 180 char
     */
    public function testUsernameBiggerThanLimit()
    {
        $this->getValidationErrors($this->getEntity()->setUsername(str_repeat('A', 181)), 1);
    }

    /**
     * Test with an already used username
     */
    public function testUsernameAlreadyUsed()
    {
        $this->getValidationErrors($this->getEntity()->setUsername('test_user'), 1);
    }

    /**
     * Test with empty password
     */
    public function testPasswordEmpty()
    {
        $this->getValidationErrors($this->getEntity()->setPassword(''), 1);
    }

    /**
     * Test with null password
     */
    public function testPasswordNull()
    {
        $this->expectException(\TypeError::class);
        $this->getEntity()->setPassword(null);
    }

    /**
     * Test with empty email
     */
    public function testEmailEmpty()
    {
        $this->getValidationErrors($this->getEntity()->setEmail(''), 2);
    }

    /**
     * Test with null email
     */
    public function testEmailNull()
    {
        $this->expectException(\TypeError::class);
        $this->getEntity()->setEmail(null);
    }

    /**
     * Test with email bigger than 255 chars
     */
    public function testEmailBiggerThanLimit()
    {
        $this->getValidationErrors($this->getEntity()->setEmail(str_repeat('A', 255)), 1);
    }

    public function testEmailAlreadyUsed()
    {
        $this->getValidationErrors($this->getEntity()->setEmail('user@localhost.com'), 1);
    }

    /**
     * @param $email
     * @return void
     * @dataProvider invalidEmailProvider
     */
    public function testEmailNotValid($email)
    {
        $this->getValidationErrors($this->getEntity()->setEmail($email), 1);
    }

    /**
     * @return array
     */
    public function invalidEmailProvider(): array
    {
        return [
            ['test'],
            [123],
            [-1],
            [1.123],
            ['test@'],
            ['test@test'],
            ['test@test.']
        ];
    }

    /**
     * Test getTasks(), addTask() and removeTask() methods
     */
    public function testTasks()
    {
        $user = $this->getEntity();

        for ($i = 0; $i < 2; $i++) {
            $task = (new Task())
                ->setTitle("test_$i")
                ->setContent("test_$i");
            $user->addTask($task);
        }

        $tasks = $user->getTasks();

        $this->assertSame(2, count($user->getTasks()));
        $this->assertInstanceOf(Task::class, $tasks[0]);
        $this->assertInstanceOf(Task::class, $tasks[1]);

        $user->removeTask($tasks[0]);

        $this->assertSame(1, count($user->getTasks()));

        $this->expectException(\TypeError::class);
        $user->removeTask($tasks[100]);
    }


    /**
     * @param User $user
     * @param int $numberOfExpectedErrors
     * @return ConstraintViolationList
     */
    private function getValidationErrors(User $user, int $numberOfExpectedErrors = 0): ConstraintViolationList
    {
        $errors = $this->validator->validate($user);
        $this->assertCount($numberOfExpectedErrors, $errors);

        return $errors;
    }
}
