<?php

use app\forms\SubscriptionForm;
use app\models\Author;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var Author $author */
/** @var SubscriptionForm $subscriptionForm */

$this->title = $author->full_name;
?>
<h1><?= Html::encode($author->full_name) ?></h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <div class="actions">
        <?= Html::a(Yii::t('app', 'Редактировать'), ['update', 'id' => $author->id], ['class' => 'button']) ?>
        <?= Html::beginForm(['delete', 'id' => $author->id], 'post') ?>
        <?= Html::submitButton(Yii::t('app', 'Удалить')) ?>
        <?= Html::endForm() ?>
    </div>
<?php endif; ?>

<?php if (Yii::$app->user->isGuest): ?>
    <h2><?= Yii::t('app', 'Подписка на новые книги') ?></h2>
    <?php $form = ActiveForm::begin(['action' => ['subscribe', 'id' => $author->id]]); ?>
    <?= $form->field($subscriptionForm, 'phone')->textInput(['placeholder' => '+79991234567']) ?>
    <div class="actions">
        <?= Html::submitButton(Yii::t('app', 'Подписаться')) ?>
    </div>
    <?php ActiveForm::end(); ?>
<?php endif; ?>

<h2><?= Yii::t('app', 'Книги автора') ?></h2>
<?php if ($author->books === []): ?>
    <p><?= Yii::t('app', 'Книг пока нет.') ?></p>
<?php else: ?>
    <ul>
        <?php foreach ($author->books as $book): ?>
            <li><?= Html::a(Html::encode($book->title), ['book/view', 'id' => $book->id]) ?>, <?= (int) $book->release_year ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
