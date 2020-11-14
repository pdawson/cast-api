<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;

class UnauthorisedException extends AuthorizationException
{
}
