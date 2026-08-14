<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var string $content */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title ?: 'Book Catalog') ?></title>
    <style>
        body { color: #1f2933; font-family: Arial, sans-serif; line-height: 1.5; margin: 0; }
        header, main { margin: 0 auto; max-width: 1040px; padding: 20px; }
        header { align-items: center; border-bottom: 1px solid #d9e2ec; display: flex; gap: 16px; justify-content: space-between; }
        nav { display: flex; flex-wrap: wrap; gap: 12px; }
        a { color: #0b5cab; text-decoration: none; }
        a:hover { text-decoration: underline; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border-bottom: 1px solid #d9e2ec; padding: 10px; text-align: left; vertical-align: top; }
        label { display: block; font-weight: 700; margin-top: 12px; }
        input, select, textarea { box-sizing: border-box; max-width: 560px; padding: 8px; width: 100%; }
        select[multiple] { min-height: 140px; }
        button, .button { background: #0b5cab; border: 0; color: #fff; cursor: pointer; display: inline-block; padding: 8px 12px; }
        .actions { display: flex; flex-wrap: wrap; gap: 8px; margin: 16px 0; }
        .notice { background: #edf7ed; border: 1px solid #8fbc8f; margin: 12px 0; padding: 10px; }
        .error { background: #fff1f0; border: 1px solid #ffa39e; margin: 12px 0; padding: 10px; }
        .help-block, .hint-block { color: #b42318; margin-top: 4px; }
        .cover { max-height: 280px; max-width: 220px; }
    </style>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<header>
    <strong><?= Html::a('Каталог книг', ['book/index']) ?></strong>
    <nav>
        <?= Html::a('Книги', ['book/index']) ?>
        <?= Html::a('Авторы', ['author/index']) ?>
        <?= Html::a('Отчет', ['report/authors']) ?>
        <?php if (Yii::$app->user->isGuest): ?>
            <?= Html::a('Войти', ['auth/login']) ?>
            <?= Html::a('Регистрация', ['auth/signup']) ?>
        <?php else: ?>
            <span><?= Html::encode(Yii::$app->user->identity->username) ?></span>
            <?php $form = \yii\widgets\ActiveForm::begin(['action' => ['auth/logout'], 'method' => 'post', 'options' => ['style' => 'display:inline']]); ?>
            <?= Html::submitButton('Выйти') ?>
            <?php \yii\widgets\ActiveForm::end(); ?>
        <?php endif; ?>
    </nav>
</header>
<main>
    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
        <div class="<?= $type === 'error' ? 'error' : 'notice' ?>">
            <?= Html::encode($message) ?>
        </div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
