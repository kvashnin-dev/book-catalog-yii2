<?php

use app\forms\LoginForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LoginForm $form */

$this->title = 'Вход';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $activeForm = ActiveForm::begin(); ?>
<?= $activeForm->field($form, 'username')->textInput(['autofocus' => true]) ?>
<?= $activeForm->field($form, 'password')->passwordInput() ?>
<?= $activeForm->field($form, 'rememberMe')->checkbox() ?>
<div class="actions">
    <?= Html::submitButton('Войти') ?>
</div>
<?php ActiveForm::end(); ?>
