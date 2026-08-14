<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $title
 * @property int $release_year
 * @property string $description
 * @property string $isbn
 * @property string|null $cover_url
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author[] $authors
 */
class Book extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%book}}';
    }

    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'isbn'], 'trim'],
            [['title', 'release_year', 'description', 'isbn'], 'required'],
            [['release_year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 32],
            [['cover_url'], 'string', 'max' => 1024],
            [['isbn'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'release_year' => 'Год выпуска',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'cover_url' => 'Фото главной страницы',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлена',
        ];
    }

    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id'])
            ->orderBy(['full_name' => SORT_ASC]);
    }

    public function getAuthorNames(): string
    {
        return implode(', ', array_map(
            static fn (Author $author): string => $author->full_name,
            $this->authors
        ));
    }
}
