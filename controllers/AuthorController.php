<?php

declare(strict_types=1);

namespace app\controllers;

use app\exceptions\EntityNotFoundException;
use app\forms\SubscriptionForm;
use app\models\Author;
use app\repositories\AuthorRepository;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Контроллер просмотра и управления авторами.
 */
class AuthorController extends Controller
{
    private AuthorRepository $authors;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param AuthorRepository $authors
     * @param array<string, mixed> $config
     */
    public function __construct($id, $module, AuthorRepository $authors, $config = [])
    {
        $this->authors = $authors;
        parent::__construct($id, $module, $config);
    }

    /**
     * Настраивает доступы и HTTP-методы.
     *
     * @return array<string, mixed>
     */
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
            'authors' => $this->authors->all(),
        ]);
    }

    /**
     * Карточка автора.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'author' => $this->author($id),
            'subscriptionForm' => new SubscriptionForm($id),
        ]);
    }

    /**
     * Создание автора.
     *
     * @return Response|string
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): Response|string
    {
        $author = new Author();

        try {
            if ($author->load(Yii::$app->request->post()) && $author->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Автор добавлен.'));

                return $this->redirect(['view', 'id' => $author->id]);
            }
        } catch (\Throwable $exception) {
            throw new ServerErrorHttpException(Yii::t('app', 'Не удалось сохранить автора.'), 0, $exception);
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
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $author = $this->author($id);

        try {
            if ($author->load(Yii::$app->request->post()) && $author->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Автор обновлен.'));

                return $this->redirect(['view', 'id' => $author->id]);
            }
        } catch (\Throwable $exception) {
            throw new ServerErrorHttpException(Yii::t('app', 'Не удалось обновить автора.'), 0, $exception);
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
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete(int $id): Response
    {
        try {
            if ($this->author($id)->delete() === false) {
                throw new ServerErrorHttpException(Yii::t('app', 'Не удалось удалить автора.'));
            }
        } catch (NotFoundHttpException|ServerErrorHttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ServerErrorHttpException(Yii::t('app', 'Не удалось удалить автора.'), 0, $exception);
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'Автор удален.'));

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
            Yii::$app->session->setFlash('success', Yii::t('app', 'Подписка оформлена.'));
        } else {
            Yii::$app->session->setFlash('error', implode(' ', $form->getFirstErrors()));
        }

        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Маппит доменную ошибку поиска в HTTP 404.
     *
     * @param int $id
     * @return Author
     * @throws NotFoundHttpException
     */
    private function author(int $id): Author
    {
        try {
            return $this->authors->get($id);
        } catch (EntityNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), 0, $exception);
        }
    }
}
