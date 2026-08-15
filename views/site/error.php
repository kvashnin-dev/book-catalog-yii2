<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Throwable $exception */

$this->title = Yii::t('app', 'Ошибка');
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::encode($exception->getMessage()) ?></p>
