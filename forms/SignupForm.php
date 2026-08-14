<?php

namespace app\forms;

use app\models\User;
use yii\base\Model;

class SignupForm extends Model
{
    /** @var string */
    public $username = '';

    /** @var string */
    public $password = '';

    public function rules(): array
    {
        return [
            [['username', 'password'], 'trim'],
            [['username', 'password'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 64],
            [['password'], 'string', 'min' => 6, 'max' => 72],
            [['username'], 'unique', 'targetClass' => User::class, 'message' => 'Пользователь с таким логином уже существует.'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
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

        $user = new User([
            'username' => $this->username,
        ]);
        $user->setPassword($this->password);
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }
}
