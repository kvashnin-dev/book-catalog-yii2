<?php

use app\forms\BookForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var BookForm $form */
/** @var array $authors */

$this->title = $form->getBook()->isNewRecord ? Yii::t('app', 'Новая книга') : Yii::t('app', 'Редактирование книги');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if ($authors === []): ?>
    <p><?= Yii::t('app', 'Сначала добавьте автора.') ?></p>
    <div class="actions">
        <?= Html::a(Yii::t('app', 'Добавить автора'), ['author/create'], ['class' => 'button']) ?>
    </div>
<?php else: ?>
    <?php $activeForm = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $activeForm->field($form, 'title')->textInput(['maxlength' => true, 'autofocus' => true]) ?>
    <?= $activeForm->field($form, 'release_year')->input('number') ?>
    <?= $activeForm->field($form, 'isbn')->textInput(['maxlength' => true]) ?>
    <?= $activeForm->field($form, 'description')->textarea(['rows' => 6]) ?>
    <?= $activeForm->field($form, 'authorIds')->listBox($authors, ['multiple' => true]) ?>
    <?= $activeForm->field($form, 'coverFile')->fileInput() ?>
    <div class="actions">
        <?= Html::submitButton(Yii::t('app', 'Сохранить')) ?>
        <?= Html::a(Yii::t('app', 'Отмена'), $form->getBook()->isNewRecord ? ['index'] : ['view', 'id' => $form->getBook()->id]) ?>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>
