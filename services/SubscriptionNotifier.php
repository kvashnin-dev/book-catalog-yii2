<?php

namespace app\services;

use app\models\Book;
use app\models\Subscription;
use Yii;

class SubscriptionNotifier
{
    public function __construct(
        private readonly SmsPilotClient $smsPilot
    ) {
    }

    /**
     * Уведомляет подписчиков авторов о новой книге.
     *
     * @param Book $book
     * @return void
     */
    public function notifyAboutNewBook(Book $book): void
    {
        $authorIds = array_map(static fn ($author): int => $author->id, $book->authors);

        if ($authorIds === []) {
            return;
        }

        $subscriptions = Subscription::find()
            ->where(['author_id' => $authorIds])
            ->all();
        $phones = array_unique(array_map(static fn (Subscription $subscription): string => $subscription->phone, $subscriptions));

        foreach ($phones as $phone) {
            try {
                $this->smsPilot->send($phone, $this->message($book));
            } catch (\Throwable $exception) {
                Yii::warning($exception->getMessage(), __METHOD__);
            }
        }
    }

    private function message(Book $book): string
    {
        return sprintf(
            'В каталог добавлена книга "%s" (%d). Автор(ы): %s.',
            $book->title,
            $book->release_year,
            $book->getAuthorNames()
        );
    }
}
