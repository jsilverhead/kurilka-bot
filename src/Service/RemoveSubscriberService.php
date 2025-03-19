<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;

class RemoveSubscriberService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository
    ) {}

    /**
     * @psalm-param non-empty-string $chatId
     */
    public function remove(Subscriber $subscriber): void
    {
        $this->subscriberRepository->remove($subscriber);
    }
}
