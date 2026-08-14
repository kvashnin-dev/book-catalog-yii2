<?php

namespace app\controllers;

use app\forms\SubscriptionForm;
use app\models\Author;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class AuthorController extends Controller
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
                    'subscribe' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Список авторов.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index', [
            'authors' => Author::find()->orderBy(['full_name' => SORT_ASC])->all(),
        ]);
    }

    /**
     * Карточка автора.
     *
     * @param int $id
     * @return string
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'author' => $this->findModel($id),
            'subscriptionForm' => new SubscriptionForm($id),
        ]);
    }

    /**
     * Создание автора.
     *
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $author = new Author();

        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            Yii::$app->session->setFlash('success', 'Автор добавлен.');

            return $this->redirect(['view', 'id' => $author->id]);
        }

        return $this->render('form', [
            'author' => $author,
        ]);
    }

    /**
     * Редактирование автора.
     *
     * @param int $id
     * @return Response|string
     */
    public function actionUpdate(int $id): Response|string
    {
        $author = $this->findModel($id);

        if ($author->load(Yii::$app->request->post()) && $author->save()) {
            Yii::$app->session->setFlash('success', 'Автор обновлен.');

            return $this->redirect(['view', 'id' => $author->id]);
        }

        return $this->render('form', [
            'author' => $author,
        ]);
    }

    /**
     * Удаление автора.
     *
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->delete();
        Yii::$app->session->setFlash('success', 'Автор удален.');

        return $this->redirect(['index']);
    }

    /**
     * Подписка гостя на новые книги автора.
     *
     * @param int $id
     * @return Response
     */
    public function actionSubscribe(int $id): Response
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['view', 'id' => $id]);
        }

        $form = new SubscriptionForm($id);

        if ($form->load(Yii::$app->request->post()) && $form->subscribe()) {
            Yii::$app->session->setFlash('success', 'Подписка оформлена.');
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $form->getFirstErrors()));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    private function findModel(int $id): Author
    {
        $author = Author::findOne($id);

        if ($author === null) {
            throw new NotFoundHttpException('Автор не найден.');
        }

        return $author;
    }
}
