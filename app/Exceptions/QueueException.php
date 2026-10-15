<?php

namespace App\Exceptions;

use DomainException;

/**
 * Thrown when a queue business rule is violated.
 *
 * The message is safe to show to the user, so controllers can catch this
 * exception and flash the message back to the form.
 */
class QueueException extends DomainException
{
}