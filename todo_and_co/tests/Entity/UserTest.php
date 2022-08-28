<?php

namespace Tests\Entity;

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
     * @return User
     */
    public function getEntity(): User
    {
        return (new User())
            ->setUsername('TestUser')
            ->setPassword('p@ssw0rd')
            ->setEmail('email@example.net')
        ;
    }

    public function testValidUser()
    {
        $this->getValidationErrors($this->getEntity());
    }

    public function testUsernameEmpty()
    {
        $this->getValidationErrors($this->getEntity()->setUsername(''), 2);
    }

    public function testUsernameNull()
    {
        $this->expectException(\TypeError::class);
        $this->getEntity()->setUsername(null);
    }

    public function testUsernameBiggerThanLimit()
    {
        $this->getValidationErrors($this->getEntity()->setUsername(str_repeat('A', 181)), 1);
    }

    public function testUsernameAlreadyUsed()
    {
        $this->getValidationErrors($this->getEntity()->setUsername('test_user'), 1);
    }

    public function testPasswordEmpty()
    {
        $this->getValidationErrors($this->getEntity()->setPassword(''), 1);
    }

    public function testPasswordNull()
    {
        $this->expectException(\TypeError::class);
        $this->getEntity()->setPassword(null);
    }

    public function testEmailEmpty()
    {
        $this->getValidationErrors($this->getEntity()->setEmail(''), 2);
    }

    public function testEmailNull()
    {
        $this->expectException(\TypeError::class);
        $this->getEntity()->setEmail(null);
    }

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
        $this->getValidationErrors($this->getEntity()->setEmail($email), 1);;
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