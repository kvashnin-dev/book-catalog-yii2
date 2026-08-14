<?php

declare(strict_types=1);

namespace app\forms;

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
        return (new Subscription())->rules();
    }

    /**
     * Подписи полей.
     *
     * @return array<string, string>
     */
    public function attributeLabels(): array
    {
        return (new Subscription())->attributeLabels();
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

        $subscription = new Subscription([
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
