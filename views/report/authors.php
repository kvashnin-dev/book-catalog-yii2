<?php

use app\forms\AuthorReportForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var AuthorReportForm $form */
/** @var array $rows */

$this->title = 'ТОП-10 авторов';
?>
<h1><?= Html::encode($this->title) ?></h1>

<?php $activeForm = ActiveForm::begin(['method' => 'get']); ?>
<?= $activeForm->field($form, 'year')->input('number') ?>
<div class="actions">
    <?= Html::submitButton('Показать') ?>
</div>
<?php ActiveForm::end(); ?>

<table>
    <thead>
    <tr>
        <th>Место</th>
        <th>Автор</th>
        <th>Книг за год</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $index => $row): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td><?= Html::a(Html::encode($row['full_name']), ['author/view', 'id' => $row['author_id']]) ?></td>
            <td><?= (int) $row['books_count'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
