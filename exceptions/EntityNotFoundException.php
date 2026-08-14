<?php

declare(strict_types=1);

namespace app\exceptions;

use RuntimeException;

/**
 * Исключение для ситуации, когда доменная сущность не найдена.
 */
class EntityNotFoundException extends RuntimeException
{
}
