<?php

declare(strict_types=1);

namespace app\repositories;

use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;
use yii\db\Query;

/**
 * Репозиторий отчетов по авторам.
 */
class AuthorReportRepository
{
    /**
     * Возвращает ТОП-10 авторов по количеству книг за год.
     *
     * @param int $year
     * @return list<array{author_id: int|string, full_name: string, books_count: int|string}>
     */
    public function getTopByYear(int $year): array
    {
        return (new Query())
            ->select([
                'author_id' => 'a.id',
                'full_name' => 'a.full_name',
                'books_count' => 'COUNT(b.id)',
            ])
            ->from(['a' => Author::tableName()])
            ->innerJoin(['ba' => BookAuthor::tableName()], 'ba.author_id = a.id')
            ->innerJoin(['b' => Book::tableName()], 'b.id = ba.book_id AND b.release_year = :year', ['year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['books_count' => SORT_DESC, 'full_name' => SORT_ASC])
            ->limit(10)
            ->all();
    }
}
