<?php

declare(strict_types=1);

namespace app\forms;

use app\models\Author;
use app\models\Subscription;
use Yii;
use yii\base\Model;

/**
 * Форма подписки гостя на новые книги автора.
 */
class SubscriptionForm extends Model
{
    public int $author_id;

    public string $phone = '';

    /**
     * @param int $authorId
     * @param array<string, mixed> $config
     */
    public function __construct(int $authorId, array $config = [])
    {
        $this->author_id = $authorId;
        parent::__construct($config);
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
            [['author_id'], 'integer'],
            [['phone'], 'trim'],
            [['phone'], 'string', 'max' => 32],
            [
                ['phone'],
                'match',
                'pattern' => '/^\+?[0-9]{10,15}$/',
                'message' => Yii::t('app', 'Укажите телефон в международном формате.'),
            ],
            [['author_id'], 'exist', 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
            [
                ['author_id', 'phone'],
                'unique',
                'targetClass' => Subscription::class,
                'targetAttribute' => ['author_id' => 'author_id', 'phone' => 'phone'],
                'message' => Yii::t('app', 'Подписка на этого автора уже оформлена.'),
            ],
        ];
    }

    /**
     * Подписи полей.
     *
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return [
            'author_id' => Yii::t('app', 'Автор'),
            'phone' => Yii::t('app', 'Телефон'),
        ];
    }

    /**
     * Нормализует телефон перед валидацией формы.
     *
     * @return bool
     */
    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        $this->phone = (string) preg_replace('/[^\d+]/', '', $this->phone);

        return true;
    }

    /**
     * Оформляет подписку гостя.
     *
     * @return bool
     */
    public function subscribe(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $subscription = Yii::$container->get(Subscription::class, [], [
            'author_id' => $this->author_id,
            'phone' => $this->phone,
        ]);

        if (!$subscription->save()) {
            $this->addErrors($subscription->getErrors());

            return false;
        }

        return true;
    }
}
