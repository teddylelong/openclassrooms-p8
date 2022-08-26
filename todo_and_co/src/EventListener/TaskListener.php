<?php

namespace App\EventListener;

use App\Entity\Task;
use Doctrine\ORM\Mapping\PrePersist;
use Symfony\Component\Security\Core\Security;

/**
 * this class allow to perform some operations before recording or updating a Task
 */
class TaskListener
{
    private $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Before recording in DB, link the current authenticated user to created task
     *
     * @param Task $task
     * @return void
     */
    #[PrePersist]
    public function prePersist(Task $task): void
    {
        if ($task->getUser()) {
            return;
        }

        if ($this->security->getUser()) {
            $task->setUser($this->security->getUser());
        }
    }
}
