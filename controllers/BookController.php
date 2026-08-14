<?php

namespace app\controllers;

use app\forms\BookForm;
use app\models\Author;
use app\models\Book;
use app\services\S3Storage;
use app\services\SmsPilotClient;
use app\services\SubscriptionNotifier;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BookController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Список книг.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index', [
            'books' => Book::find()->with('authors')->orderBy(['created_at' => SORT_DESC])->all(),
        ]);
    }

    /**
     * Карточка книги.
     *
     * @param int $id
     * @return string
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'book' => $this->findModel($id),
        ]);
    }

    /**
     * Создание книги.
     *
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new BookForm();

        if (Yii::$app->request->isPost) {
            $book = $form->load(Yii::$app->request->post()) ? $form->save($this->storage()) : null;

            if ($book !== null) {
                $this->notifier()->notifyAboutNewBook($book);
                Yii::$app->session->setFlash('success', 'Книга добавлена.');

                return $this->redirect(['view', 'id' => $book->id]);
            }
        }

        return $this->render('form', [
            'form' => $form,
            'authors' => $this->authorsList(),
        ]);
    }

    /**
     * Редактирование книги.
     *
     * @param int $id
     * @return Response|string
     */
    public function actionUpdate(int $id): Response|string
    {
        $form = new BookForm($this->findModel($id));

        if (Yii::$app->request->isPost) {
            $book = $form->load(Yii::$app->request->post()) ? $form->save($this->storage()) : null;

            if ($book !== null) {
                Yii::$app->session->setFlash('success', 'Книга обновлена.');

                return $this->redirect(['view', 'id' => $book->id]);
            }
        }

        return $this->render('form', [
            'form' => $form,
            'authors' => $this->authorsList(),
        ]);
    }

    /**
     * Удаление книги.
     *
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Книга удалена.');

        return $this->redirect(['index']);
    }

    private function findModel(int $id): Book
    {
        $book = Book::find()->with('authors')->where(['id' => $id])->one();

        if ($book === null) {
            throw new NotFoundHttpException('Книга не найдена.');
        }

        return $book;
    }

    private function authorsList(): array
    {
        return Author::find()
            ->select(['full_name', 'id'])
            ->orderBy(['full_name' => SORT_ASC])
            ->indexBy('id')
            ->column();
    }

    private function storage(): S3Storage
    {
        return new S3Storage(Yii::$app->params['s3']);
    }

    private function notifier(): SubscriptionNotifier
    {
        return new SubscriptionNotifier(new SmsPilotClient(Yii::$app->params['smsPilot']));
    }
}
