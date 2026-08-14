<?php

declare(strict_types=1);

namespace app\controllers;

use app\exceptions\EntityNotFoundException;
use app\forms\BookForm;
use app\models\Book;
use app\repositories\AuthorRepository;
use app\repositories\BookRepository;
use app\services\BookService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Контроллер просмотра и управления книгами.
 */
class BookController extends Controller
{
    private BookRepository $books;
    private AuthorRepository $authors;
    private BookService $bookService;

    /**
     * @param string $id
     * @param \yii\base\Module $module
     * @param BookRepository $books
     * @param AuthorRepository $authors
     * @param BookService $bookService
     * @param array<string, mixed> $config
     */
    public function __construct(
        $id,
        $module,
        BookRepository $books,
        AuthorRepository $authors,
        BookService $bookService,
        $config = []
    ) {
        $this->books = $books;
        $this->authors = $authors;
        $this->bookService = $bookService;
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
            'books' => $this->books->all(),
        ]);
    }

    /**
     * Карточка книги.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'book' => $this->book($id),
        ]);
    }

    /**
     * Создание книги.
     *
     * @return Response|string
     * @throws ServerErrorHttpException
     */
    public function actionCreate(): Response|string
    {
        $form = new BookForm();

        if (Yii::$app->request->isPost) {
            try {
                $form->load(Yii::$app->request->post());
                $form->loadCoverFile();
                $book = $form->validate() ? $this->bookService->create($form) : null;

                if ($book !== null) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Книга добавлена.'));

                    return $this->redirect(['view', 'id' => $book->id]);
                }
            } catch (\Throwable $exception) {
                throw new ServerErrorHttpException(Yii::t('app', 'Не удалось сохранить книгу.'), 0, $exception);
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
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $form = new BookForm($this->book($id));

        if (Yii::$app->request->isPost) {
            try {
                $form->load(Yii::$app->request->post());
                $form->loadCoverFile();
                $book = $form->validate() ? $this->bookService->update($form) : null;

                if ($book !== null) {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Книга обновлена.'));

                    return $this->redirect(['view', 'id' => $book->id]);
                }
            } catch (\Throwable $exception) {
                throw new ServerErrorHttpException(Yii::t('app', 'Не удалось обновить книгу.'), 0, $exception);
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
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     */
    public function actionDelete(int $id): Response
    {
        try {
            if ($this->book($id)->delete() === false) {
                throw new ServerErrorHttpException(Yii::t('app', 'Не удалось удалить книгу.'));
            }
        } catch (NotFoundHttpException|ServerErrorHttpException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw new ServerErrorHttpException(Yii::t('app', 'Не удалось удалить книгу.'), 0, $exception);
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'Книга удалена.'));

        return $this->redirect(['index']);
    }

    /**
     * Маппит доменную ошибку поиска в HTTP 404.
     *
     * @param int $id
     * @return Book
     * @throws NotFoundHttpException
     */
    private function book(int $id): Book
    {
        try {
            return $this->books->get($id);
        } catch (EntityNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), 0, $exception);
        }
    }

    /**
     * Возвращает список авторов для формы книги.
     *
     * @return array<int, string>
     */
    private function authorsList(): array
    {
        return $this->authors->listForSelect();
    }
}
