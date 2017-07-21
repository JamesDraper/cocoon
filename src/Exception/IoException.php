<?php
declare(strict_types=1);

namespace Cocoon\Exception;

use RuntimeException;

/**
 * Exception thrown due to a file system problem
 * loading template files/reading template directories etc.
 */
class IoException extends RuntimeException
{
}
