<?php

declare(strict_types=1);

namespace app\forms;

use Yii;
use yii\base\Model;

/**
 * Форма фильтра публичного отчета по авторам.
 */
class AuthorReportForm extends Model
{
    public int|string $year;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $this->year = (int) date('Y');
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
            [['year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
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
            'year' => Yii::t('app', 'Год'),
        ];
    }
}
