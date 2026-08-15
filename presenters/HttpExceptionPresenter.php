<?php

declare(strict_types=1);

namespace app\presenters;

use app\exceptions\EntityNotFoundException;
use yii\web\NotFoundHttpException;

/**
 * Преобразует доменные ошибки в HTTP-исключения.
 */
class HttpExceptionPresenter
{
    /**
     * Возвращает результат поиска или HTTP 404.
     *
     * @template T
     * @param callable(): T $resolver
     * @return T
     * @throws NotFoundHttpException
     */
    public function notFound(callable $resolver): mixed
    {
        try {
            return $resolver();
        } catch (EntityNotFoundException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), 0, $exception);
        }
    }
}
