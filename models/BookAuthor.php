<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * @property int $book_id
 * @property int $author_id
 */
class BookAuthor extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%book_author}}';
    }
}
