<?php

namespace App\Repository;

use App\Entity\Subscriber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscriber>
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function add(Subscriber $subscriber): void
    {
        $this->getEntityManager()->persist($subscriber);
    }

    public function remove(Subscriber $subscriber): void
    {
        $this->getEntityManager()->remove($subscriber);
    }

    public function getSubscriberByChatId(string $chatId): ?Subscriber
    {
        /**
         * @psalm-var Subscriber|null $subscriber
         */
        $subscriber = $this->createQueryBuilder("sb")
            ->select("sb")
            ->where("sb.chatId = :chatId")
            ->setParameter("chatId", $chatId)
            ->getQuery()
            ->getOneOrNullResult();

        return $subscriber;
    }

    public function getSubscribersExceptChatIds(string $chatId): array
    {
        /** @psalm-var list<Subscriber> $subscribers */
        $subscribers = $this->createQueryBuilder("sb")
            ->where("sb.chatId != :chatId")
            ->setParameter("chatId", $chatId)
            ->getQuery()
            ->getResult();

        return $subscribers;
    }
}
