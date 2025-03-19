<?php

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

require "../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../", ".env.local");
$dotenv->load();

$token = $_ENV["TELEGRAM_BOT_TOKEN"];

$bot = new TelegramBot\Api\BotApi($token);

// URL зависит от cloudflare
$webhookUrl = "https://oaks-recall-promised-organic.trycloudflare.com/webhook";
$bot->setWebhook(url: $webhookUrl);

echo "Вебхук установлен на: " . $webhookUrl . "\n";
