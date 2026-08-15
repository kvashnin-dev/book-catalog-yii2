<?php

declare(strict_types=1);

namespace app\controllers;

use app\forms\AuthorReportForm;
use app\repositories\AuthorReportRepository;
use Yii;
use yii\web\Controller;

/**
 * Контроллер публичных отчетов.
 */
class ReportController extends Controller
{
    /**
     * ТОП-10 авторов по количеству книг за выбранный год.
     *
     * @return string
     */
    public function actionAuthors(): string
    {
        $form = Yii::$container->get(AuthorReportForm::class);
        $form->load(Yii::$app->request->get());

        if (!$form->validate()) {
            $form->year = (int) date('Y');
        }

        return $this->render('authors', [
            'form' => $form,
            'rows' => $this->module
                ->get(AuthorReportRepository::class)
                ->getTopByYear((int) $form->year),
        ]);
    }
}
