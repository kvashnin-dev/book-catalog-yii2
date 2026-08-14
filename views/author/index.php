<?php

use app\models\Author;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var Author[] $authors */

$this->title = 'Авторы';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php if (!Yii::$app->user->isGuest): ?>
    <div class="actions">
        <?= Html::a('Добавить автора', ['create'], ['class' => 'button']) ?>
    </div>
<?php endif; ?>

<table>
    <thead>
    <tr>
        <th>ФИО</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($authors as $author): ?>
        <tr>
            <td><?= Html::encode($author->full_name) ?></td>
            <td><?= Html::a('Открыть', ['view', 'id' => $author->id]) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
