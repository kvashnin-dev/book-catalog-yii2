<?php

declare(strict_types=1);

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property string $auth_key
 * @property int $created_at
 * @property int $updated_at
 */
class User extends ActiveRecord implements IdentityInterface
{
    /**
     * Имя таблицы пользователей.
     *
     * @return string
     */
    public static function tableName(): string
    {
        return '{{%user}}';
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
            [['username', 'password_hash', 'auth_key'], 'required'],
            [['username'], 'string', 'max' => 64],
            [['password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
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
            'username' => \Yii::t('app', 'Логин'),
            'password_hash' => \Yii::t('app', 'Пароль'),
        ];
    }

    /**
     * Ищет пользователя по ID для Yii auth.
     *
     * @param int|string $id
     * @return self|null
     */
    public static function findIdentity($id): ?self
    {
        return self::findOne((int) $id);
    }

    /**
     * Токены доступа в web-приложении не используются.
     *
     * @param mixed $token
     * @param mixed $type
     * @return self|null
     */
    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        return null;
    }

    /**
     * Ищет пользователя по логину.
     *
     * @param string $username
     * @return self|null
     */
    public static function findByUsername(string $username): ?self
    {
        return self::findOne(['username' => $username]);
    }

    /**
     * Возвращает ID пользователя.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Возвращает auth key для cookie login.
     *
     * @return string
     */
    public function getAuthKey(): string
    {
        return $this->auth_key;
    }

    /**
     * Проверяет auth key.
     *
     * @param mixed $authKey
     * @return bool
     */
    public function validateAuthKey($authKey): bool
    {
        return hash_equals($this->auth_key, (string) $authKey);
    }

    /**
     * Проверяет пароль.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Устанавливает хеш пароля.
     *
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Генерирует auth key.
     *
     * @return void
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = \Yii::$app->security->generateRandomString(32);
    }
}
