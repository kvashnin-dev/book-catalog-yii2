<?php

namespace app\forms;

use app\models\User;
use yii\base\Model;

class LoginForm extends Model
{
    /** @var string */
    public $username = '';

    /** @var string */
    public $password = '';

    /** @var bool|string */
    public $rememberMe = true;

    private ?User $user = null;

    public function rules(): array
    {
        return [
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'required'],
            [['rememberMe'], 'boolean'],
            [['password'], 'validatePassword'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }

    /**
     * Проверяет пароль пользователя.
     *
     * @param string $attribute
     * @return void
     */
    public function validatePassword(string $attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        $user = $this->getUser();

        if ($user === null || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Неверный логин или пароль.');
        }
    }

    /**
     * Авторизует пользователя.
     *
     * @return bool
     */
    public function login(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        return \Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    private function getUser(): ?User
    {
        if ($this->user === null) {
            $this->user = User::findByUsername($this->username);
        }

        return $this->user;
    }
}
