<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $author_id
 * @property string $phone
 * @property int $created_at
 *
 * @property Author $author
 */
class Subscription extends ActiveRecord
{
    /**
     * Имя таблицы подписок.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%subscription}}';
    }

    /**
     * Правила валидации.
     *
     * @return array<int, array<mixed>>
     */
    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            [['phone'], 'trim'],
            [['phone'], 'string', 'max' => 32],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9]{10,15}$/', 'message' => \Yii::t('app', 'Укажите телефон в международном формате.')],
            [['author_id'], 'exist', 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone'], 'message' => \Yii::t('app', 'Подписка на этого автора уже оформлена.')],
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
            'author_id' => \Yii::t('app', 'Автор'),
            'phone' => \Yii::t('app', 'Телефон'),
            'created_at' => \Yii::t('app', 'Дата подписки'),
        ];
    }

    /**
     * Нормализует телефон и дату перед сохранением.
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->phone = preg_replace('/[^\d+]/', '', $this->phone);

        if ($insert) {
            $this->created_at = time();
        }

        return true;
    }

    /**
     * Автор, на которого оформлена подписка.
     *
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}
