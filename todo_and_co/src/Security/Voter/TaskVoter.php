<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT   = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    private $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Task;
    }

    /**
     * @param string $attribute
     * @param $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject);

            case self::DELETE:
                return $this->canDelete($subject);
        }
        return false;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function canEdit(Task $task)
    {
        if ($task->getUser() === $this->security->getUser()) {
            return true;
        }
        return false;
    }

    /**
     * @param Task $task
     * @return bool
     */
    public function canDelete(Task $task)
    {
        if ($task->getUser() === $this->security->getUser()) {
            return true;
        }
        return false;
    }
}
