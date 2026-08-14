<?php

namespace app\controllers;

use app\forms\AuthorReportForm;
use app\models\Author;
use app\models\Book;
use app\models\BookAuthor;
use Yii;
use yii\db\Query;
use yii\web\Controller;

class ReportController extends Controller
{
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
            'rows' => $this->topAuthors((int) $form->year),
        ]);
    }

    private function topAuthors(int $year): array
    {
        return (new Query())
            ->select([
                'author_id' => 'a.id',
                'full_name' => 'a.full_name',
                'books_count' => 'COUNT(b.id)',
            ])
            ->from(['a' => Author::tableName()])
            ->innerJoin(['ba' => BookAuthor::tableName()], 'ba.author_id = a.id')
            ->innerJoin(['b' => Book::tableName()], 'b.id = ba.book_id AND b.release_year = :year', ['year' => $year])
            ->groupBy(['a.id', 'a.full_name'])
            ->orderBy(['books_count' => SORT_DESC, 'full_name' => SORT_ASC])
            ->limit(10)
            ->all();
    }
}
