<?php

namespace app\forms;

use yii\base\Model;

class AuthorReportForm extends Model
{
    /** @var int|string */
    public $year;

    public function __construct(array $config = [])
    {
        $this->year = (int) date('Y');
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int) date('Y') + 1],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'year' => 'Год',
        ];
    }
}
