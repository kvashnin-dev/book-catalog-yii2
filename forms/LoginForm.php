<?php

declare(strict_types=1);

namespace app\forms;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * Форма входа пользователя.
 */
class LoginForm extends Model
{
    public string $username = '';

    public string $password = '';

    public bool|string $rememberMe = true;

    private ?User $user = null;

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
            [['rememberMe'], 'boolean'],
            [['password'], 'validatePassword'],
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
            'rememberMe' => Yii::t('app', 'Запомнить меня'),
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
            $this->addError($attribute, Yii::t('app', 'Неверный логин или пароль.'));
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

        $user = $this->getUser();

        if ($user === null) {
            return false;
        }

        return Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0);
    }

    /**
     * Возвращает пользователя по логину.
     *
     * @return User|null
     */
    private function getUser(): ?User
    {
        if ($this->user === null) {
            $this->user = User::findByUsername($this->username);
        }

        return $this->user;
    }
}
