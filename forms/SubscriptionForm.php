<?php

namespace app\forms;

use app\models\Subscription;
use yii\base\Model;

class SubscriptionForm extends Model
{
    public int $author_id;

    /** @var string */
    public $phone = '';

    public function __construct(int $authorId, array $config = [])
    {
        $this->author_id = $authorId;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return (new Subscription())->rules();
    }

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
