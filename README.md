### Kurilka Bot v0.1

Установка:
1. Установить туннель для публикации API:
````
npm install -g cloudflared                    
````
2. Добавить в env токен для бота:
````
TELEGRAM_BOT_TOKEN=!YourTelegramAPIToken!
````
3. Настроить бота на следующие команды: `/subscribe`, `/unsubscribe`, `/smoke`
4. Запустить Symfony сервер на нужном порте
5. Запустить CloudFlared тунель (порт должен совпадать с сервером symfony):
````
cloudflared tunnel --url http://localhost:8002
````
6. Настроить webhook. Вместо текущего `$webhookUrl` указать домен от CloudFlared `/webhook`
7. Запустить webhook скрипт:
````
cd scripts
php set_webhook.php
````
8. Наслаждаться работой бота