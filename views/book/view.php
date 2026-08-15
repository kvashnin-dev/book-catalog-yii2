<?php

use app\models\Book;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Book $book */

$this->title = $book->title;
?>
<h1><?= Html::encode($book->title) ?></h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <div class="actions">
        <?= Html::a(Yii::t('app', 'Редактировать'), ['update', 'id' => $book->id], ['class' => 'button']) ?>
        <?= Html::beginForm(['delete', 'id' => $book->id], 'post') ?>
        <?= Html::submitButton(Yii::t('app', 'Удалить')) ?>
        <?= Html::endForm() ?>
    </div>
<?php endif; ?>

<?php if ($book->cover_url): ?>
    <p><?= Html::img($book->cover_url, ['alt' => $book->title, 'class' => 'cover']) ?></p>
<?php endif; ?>

<dl>
    <dt><?= Yii::t('app', 'Год выпуска') ?></dt>
    <dd><?= (int) $book->release_year ?></dd>
    <dt>ISBN</dt>
    <dd><?= Html::encode($book->isbn) ?></dd>
    <dt><?= Yii::t('app', 'Авторы') ?></dt>
    <dd>
        <?php foreach ($book->authors as $author): ?>
            <?= Html::a(Html::encode($author->full_name), ['author/view', 'id' => $author->id]) ?><br>
        <?php endforeach; ?>
    </dd>
    <dt><?= Yii::t('app', 'Описание') ?></dt>
    <dd><?= nl2br(Html::encode($book->description)) ?></dd>
</dl>
