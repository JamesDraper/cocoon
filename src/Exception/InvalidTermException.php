<?php
declare(strict_types=1);

namespace Cocoon\Exception;

use LogicException;

/**
 * Exception thrown when an invalid variable name is added to  a view,
 * such as a name containing exceptions or tabs.
 */
class InvalidTermException extends LogicException
{
}
