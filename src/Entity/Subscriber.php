<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
class Subscriber
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $chatId;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): static
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __construct(string $chatId)
    {
        $this->setChatId($chatId);
        $this->setCreatedAt(new \DateTimeImmutable());
    }
}
