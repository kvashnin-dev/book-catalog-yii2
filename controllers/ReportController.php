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
        $form = $this->authorReportForm();
        $form->load(Yii::$app->request->get());

        if (!$form->validate()) {
            $form->year = (int) date('Y');
        }

        return $this->render('authors', [
            'form' => $form,
            'rows' => $this->reports()->topByYear((int) $form->year),
        ]);
    }

    /**
     * Форма отчета по авторам.
     *
     * @return AuthorReportForm
     */
    private function authorReportForm(): AuthorReportForm
    {
        return Yii::$container->get(AuthorReportForm::class);
    }

    /**
     * Репозиторий отчета по авторам.
     *
     * @return AuthorReportRepository
     */
    private function reports(): AuthorReportRepository
    {
        return $this->module->get(AuthorReportRepository::class);
    }
}
