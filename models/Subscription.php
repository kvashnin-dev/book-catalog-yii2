<?php

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
    public static function tableName(): string
    {
        return '{{%subscription}}';
    }

    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id', 'created_at'], 'integer'],
            [['phone'], 'trim'],
            [['phone'], 'string', 'max' => 32],
            [['phone'], 'match', 'pattern' => '/^\+?[0-9]{10,15}$/', 'message' => 'Укажите телефон в международном формате.'],
            [['author_id'], 'exist', 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone'], 'message' => 'Подписка на этого автора уже оформлена.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'author_id' => 'Автор',
            'phone' => 'Телефон',
            'created_at' => 'Дата подписки',
        ];
    }

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

    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}
