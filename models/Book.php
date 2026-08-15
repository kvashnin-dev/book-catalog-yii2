<?php

declare(strict_types=1);

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
    /**
     * Имя таблицы книг.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%book}}';
    }

    /**
     * Поведения модели.
     *
     * @return array<int|string, mixed>
     */
    public function behaviors(): array
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * Правила валидации.
     *
     * @return array<int, array<mixed>>
     */
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

    /**
     * Подписи атрибутов.
     *
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => \Yii::t('app', 'Название'),
            'release_year' => \Yii::t('app', 'Год выпуска'),
            'description' => \Yii::t('app', 'Описание'),
            'isbn' => 'ISBN',
            'cover_url' => \Yii::t('app', 'Фото главной страницы'),
            'created_at' => \Yii::t('app', 'Создана'),
            'updated_at' => \Yii::t('app', 'Обновлена'),
        ];
    }

    /**
     * Авторы книги.
     *
     * @return ActiveQuery
     */
    public function getAuthors(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('{{%book_author}}', ['book_id' => 'id'])
            ->orderBy(['full_name' => SORT_ASC]);
    }

    /**
     * Возвращает имена авторов одной строкой.
     *
     * @return string
     */
    public function getAuthorNames(): string
    {
        return implode(', ', array_map(
            static fn (Author $author): string => $author->full_name,
            $this->authors
        ));
    }
}
