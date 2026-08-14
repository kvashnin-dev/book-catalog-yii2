<?php

declare(strict_types=1);

namespace app\controllers;

use app\forms\LoginForm;
use app\forms\SignupForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Контроллер авторизации и регистрации пользователей.
 */
class AuthController extends Controller
{
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
                'only' => ['login', 'logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['login', 'signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Вход пользователя.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        $form = $this->loginForm();

        if ($form->load(Yii::$app->request->post()) && $form->login()) {
            return $this->goBack(['book/index']);
        }

        return $this->render('login', [
            'form' => $form,
        ]);
    }

    /**
     * Регистрация пользователя.
     *
     * @return Response|string
     */
    public function actionSignup(): Response|string
    {
        $form = $this->signupForm();

        if ($form->load(Yii::$app->request->post())) {
            $user = $form->signup();

            if ($user !== null) {
                Yii::$app->user->login($user);

                return $this->redirect(['book/index']);
            }
        }

        return $this->render('signup', [
            'form' => $form,
        ]);
    }

    /**
     * Выход пользователя.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->redirect(['book/index']);
    }

    /**
     * Форма входа пользователя.
     *
     * @return LoginForm
     */
    private function loginForm(): LoginForm
    {
        return Yii::$container->get(LoginForm::class);
    }

    /**
     * Форма регистрации пользователя.
     *
     * @return SignupForm
     */
    private function signupForm(): SignupForm
    {
        return Yii::$container->get(SignupForm::class);
    }
}
