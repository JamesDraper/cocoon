<?php
declare(strict_types=1);

namespace Cocoon\Exception;

use LogicException;

/**
 * Exception thrown when a reserved variable name is added to a view,
 * such as "content" which is reserved for emmbedding a template into another template.
 */
class ReservedTermException extends LogicException
{
}
