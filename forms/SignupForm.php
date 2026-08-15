<?php

declare(strict_types=1);

namespace app\forms;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * Форма регистрации пользователя.
 */
class SignupForm extends Model
{
    public string $username = '';

    public string $password = '';

    /**
     * Правила валидации.
     *
     * @return array<int, array<mixed>>
     */
    public function rules(): array
    {
        return [
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 64],
            [['password'], 'string', 'min' => 6, 'max' => 72],
            [['username'], 'unique', 'targetClass' => User::class, 'message' => Yii::t('app', 'Пользователь с таким логином уже существует.')],
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
            'username' => Yii::t('app', 'Логин'),
            'password' => Yii::t('app', 'Пароль'),
        ];
    }

    /**
     * Создает нового пользователя.
     *
     * @return User|null
     */
    public function signup(): ?User
    {
        if (!$this->validate()) {
            return null;
        }

        $user = Yii::$container->get(User::class, [], [
            'username' => $this->username,
        ]);
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
