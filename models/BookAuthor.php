<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $book_id
 * @property int $author_id
 */
class BookAuthor extends ActiveRecord
{
    /**
     * Имя таблицы связей книг и авторов.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%book_author}}';
    }
}
