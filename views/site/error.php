<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Throwable $exception */

$this->title = 'Error';
?>
<h1><?= Html::encode($this->title) ?></h1>
<p><?= Html::encode($exception->getMessage()) ?></p>
