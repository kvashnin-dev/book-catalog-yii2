<?php

declare(strict_types=1);

namespace app\services;

use app\forms\BookForm;
use app\models\Book;
use app\models\BookAuthor;
use Yii;

/**
 * Сервис сценариев создания и редактирования книг.
 */
class BookService
{
    /**
     * @param S3Storage $storage
     * @param SubscriptionNotifier $notifier
     */
    public function __construct(
        private readonly S3Storage $storage,
        private readonly SubscriptionNotifier $notifier
    ) {
    }

    /**
     * Создает книгу и уведомляет подписчиков авторов.
     *
     * @param BookForm $form
     * @return Book|null
     * @throws \Throwable
     */
    public function create(BookForm $form): ?Book
    {
        $book = $this->save($form);

        if ($book !== null) {
            $this->notifier->notifyAboutNewBook($book);
        }

        return $book;
    }

    /**
     * Обновляет книгу.
     *
     * @param BookForm $form
     * @return Book|null
     * @throws \Throwable
     */
    public function update(BookForm $form): ?Book
    {
        return $this->save($form);
    }

    /**
     * Сохраняет книгу, обложку и связи с авторами.
     *
     * @param BookForm $form
     * @return Book|null
     * @throws \Throwable
     */
    private function save(BookForm $form): ?Book
    {
        $book = $form->getBook();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $book->setAttributes($form->bookAttributes());

            if ($form->coverFile !== null) {
                $book->cover_url = $this->storage->upload($form->coverFile, 'covers');
            }

            if (!$book->save()) {
                $form->addErrors($book->getErrors());
                $transaction->rollBack();

                return null;
            }

            $this->syncAuthors($book, $form->normalizedAuthorIds());
            $transaction->commit();

            return $book;
        } catch (\Throwable $exception) {
            if ($transaction->isActive) {
                $transaction->rollBack();
            }

            throw $exception;
        }
    }

    /**
     * Синхронизирует связи книги с авторами.
     *
     * @param Book $book
     * @param list<int> $authorIds
     * @return void
     */
    private function syncAuthors(Book $book, array $authorIds): void
    {
        BookAuthor::deleteAll(['book_id' => $book->id]);
        $rows = array_map(
            static fn (int $authorId): array => [$book->id, $authorId],
            $authorIds
        );

        Yii::$app->db->createCommand()
            ->batchInsert(BookAuthor::tableName(), ['book_id', 'author_id'], $rows)
            ->execute();
    }
}
