<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\DoctrineSchema\API\Exception;

use Exception;
use Throwable;

class InvalidConfigurationException extends Exception
{
    public function __construct(string $message = '', int $code = 1, Throwable $previous = null)
    {
        parent::__construct("Invalid schema configuration: {$message}", $code, $previous);
    }
}
