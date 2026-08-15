<?php

use app\models\Book;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Book[] $books */

$this->title = Yii::t('app', 'Книги');
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <div class="actions">
        <?= Html::a(Yii::t('app', 'Добавить книгу'), ['create'], ['class' => 'button']) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
    <tr>
        <th><?= Yii::t('app', 'Название') ?></th>
        <th><?= Yii::t('app', 'Год') ?></th>
        <th><?= Yii::t('app', 'Авторы') ?></th>
        <th>ISBN</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($books as $book): ?>
        <tr>
            <td><?= Html::encode($book->title) ?></td>
            <td><?= (int) $book->release_year ?></td>
            <td><?= Html::encode($book->getAuthorNames()) ?></td>
            <td><?= Html::encode($book->isbn) ?></td>
            <td><?= Html::a(Yii::t('app', 'Открыть'), ['view', 'id' => $book->id]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
