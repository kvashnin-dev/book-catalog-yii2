<?php

use app\models\Book;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Book[] $books */

$this->title = 'Книги';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <div class="actions">
        <?= Html::a('Добавить книгу', ['create'], ['class' => 'button']) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
    <tr>
        <th>Название</th>
        <th>Год</th>
        <th>Авторы</th>
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
            <td><?= Html::a('Открыть', ['view', 'id' => $book->id]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
