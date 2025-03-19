<?php

namespace App\Service;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;

class CreateSubscriberService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository
    ) {}

    public function create(string $telegramUrl): Subscriber
    {
        $subscriber = new Subscriber($telegramUrl);

        $this->subscriberRepository->add($subscriber);

        return $subscriber;
    }
}
