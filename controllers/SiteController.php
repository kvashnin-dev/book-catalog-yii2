<?php

declare(strict_types=1);

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

/**
 * Служебный контроллер базовых страниц.
 */
class SiteController extends Controller
{
    /**
     * Стартовая страница.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index');
    }

    /**
     * Страница ошибки.
     *
     * @return string|Response
     */
    public function actionError(): string|Response
    {
        $exception = \Yii::$app->errorHandler->exception;

        if ($exception === null) {
            return $this->redirect(['site/index']);
        }

        return $this->render('error', [
            'exception' => $exception,
        ]);
    }
}
