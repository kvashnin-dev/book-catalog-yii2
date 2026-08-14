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
    private AuthorReportRepository $reports;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param AuthorReportRepository $reports
     * @param array<string, mixed> $config
     */
    public function __construct($id, $module, AuthorReportRepository $reports, $config = [])
    {
        $this->reports = $reports;
        parent::__construct($id, $module, $config);
    }

    /**
     * ТОП-10 авторов по количеству книг за выбранный год.
     *
     * @return string
     */
    public function actionAuthors(): string
    {
        $form = new AuthorReportForm();
        $form->load(Yii::$app->request->get());

        if (!$form->validate()) {
            $form->year = (int) date('Y');
        }

        return $this->render('authors', [
            'form' => $form,
            'rows' => $this->reports->topByYear((int) $form->year),
        ]);
    }
}
