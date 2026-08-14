<?php

use app\models\Author;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Author $author */

$this->title = $author->isNewRecord ? Yii::t('app', 'Новый автор') : Yii::t('app', 'Редактирование автора');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $form = ActiveForm::begin(); ?>
<?= $form->field($author, 'full_name')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
<div class="actions">
    <?= Html::submitButton(Yii::t('app', 'Сохранить')) ?>
    <?= Html::a(Yii::t('app', 'Отмена'), $author->isNewRecord ? ['index'] : ['view', 'id' => $author->id]) ?>
</div>
<?php ActiveForm::end(); ?>
