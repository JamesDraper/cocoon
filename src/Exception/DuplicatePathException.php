<?php
declare(strict_types=1);

namespace Cocoon\Exception;

use LogicException;

/**
 * Exception thrown when the same view directory is added to a factory twice.
 */
class DuplicatePathException extends LogicException
{
}
