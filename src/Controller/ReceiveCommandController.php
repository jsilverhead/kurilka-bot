<?php

namespace App\Controller;

use App\Repository\SubscriberRepository;
use App\Service\CreateSubscriberService;
use App\Service\RemoveSubscriberService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TelegramBot\Api\BotApi;

#[Route(path: "/webhook", name: "webhook")]
class ReceiveCommandController extends AbstractController
{
    /** @psalm-var non-empty-string $telegramBotToken  */
    private string $telegramBotToken;
    private BotApi $bot;
    private LoggerInterface $logger;
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SubscriberRepository $subscriberRepository,
        private CreateSubscriberService $createSubscriberService,
        private RemoveSubscriberService $removeSubscriberService,
        LoggerInterface $logger,
        string $telegramBotToken
    ) {
        $this->telegramBotToken = $telegramBotToken;
        $this->bot = new BotApi($this->telegramBotToken);

        $this->logger = $logger;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent(), true);
        } catch (JsonException) {
            throw new JsonException("Invalid data");
        }

        $this->logger->info("Incoming data:", $data);

        if (!isset($data["message"])) {
            $this->logger->error("No message in data: " . print_r($data, true));
            return new Response("Bad request", Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data["message"]["chat"])) {
            $this->logger->error("No chat in data: " . print_r($data, true));
            return new Response("Bad request", Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data["message"]["chat"]["id"])) {
            $this->logger->error("No chat in data: " . print_r($data, true));
            return new Response("Bad request", Response::HTTP_BAD_REQUEST);
        }

        $chatId = $data["message"]["chat"]["id"];
        $command = $data["message"]["text"];

        if ($command === "/subscribe") {
            $this->subscribe($chatId);
        }

        if ($command === "/unsubscribe") {
            $this->unsubscribe($chatId);
        }

        if ($command === "/smoke") {
            $this->sendMessage($chatId);
        }

        $this->entityManager->flush();

        error_log("Hello");

        return new Response("ok");
    }

    private function subscribe(string $chatId): void
    {
        $existingSubscriber = $this->subscriberRepository->getSubscriberByChatId(
            $chatId
        );

        if ($existingSubscriber) {
            $this->bot->sendMessage(
                chatId: $chatId,
                text: "Пользователь уже подписан на Kurilka bot 🤷🏻"
            );

            return;
        }

        $this->createSubscriberService->create($chatId);
        $this->bot->sendMessage(
            chatId: $chatId,
            text: "Вы подписаны на Kurilka Bot 🔔"
        );
    }

    private function unsubscribe(string $chatId): void
    {
        $existingSubscriber = $this->subscriberRepository->getSubscriberByChatId(
            $chatId
        );

        if (null === $existingSubscriber) {
            $this->bot->sendMessage(
                chatId: $chatId,
                text: "Вы не подписаны на Kurilka Bot 🤷🏻"
            );

            return;
        }

        $this->removeSubscriberService->remove($existingSubscriber);

        $this->bot->sendMessage(
            chatId: $chatId,
            text: "Вы отписались от Kurilka Bot 😢"
        );
    }

    private function sendMessage(string $chatId): void
    {
        $subscribers = $this->subscriberRepository->getSubscribersExceptChatIds(
            $chatId
        );

        foreach ($subscribers as $subscriber) {
            $this->bot->sendMessage(
                chatId: $subscriber->getChatId(),
                text: "Го курить! 🚬"
            );
        }

        $this->bot->sendMessage(
            chatId: $chatId,
            text: "Приглашение на покурить отправлено"
        );
    }
}
