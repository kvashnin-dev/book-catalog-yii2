<?php

namespace app\controllers;

use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index');
    }

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
