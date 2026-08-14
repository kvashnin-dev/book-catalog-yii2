<?php

use app\forms\SignupForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var SignupForm $form */

$this->title = 'Регистрация';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $activeForm = ActiveForm::begin(); ?>
<?= $activeForm->field($form, 'username')->textInput(['autofocus' => true]) ?>
<?= $activeForm->field($form, 'password')->passwordInput() ?>
<div class="actions">
    <?= Html::submitButton('Зарегистрироваться') ?>
</div>
<?php ActiveForm::end(); ?>
