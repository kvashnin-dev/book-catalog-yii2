<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $full_name
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Book[] $books
 */
class Author extends ActiveRecord
{
    /**
     * Имя таблицы авторов.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%author}}';
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
            [['full_name'], 'trim'],
            [['full_name'], 'required'],
            [['full_name'], 'string', 'max' => 255],
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
            'full_name' => \Yii::t('app', 'ФИО'),
            'created_at' => \Yii::t('app', 'Создан'),
            'updated_at' => \Yii::t('app', 'Обновлен'),
        ];
    }

    /**
     * Связанные книги автора.
     *
     * @return ActiveQuery
     */
    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('{{%book_author}}', ['author_id' => 'id']);
    }
}
