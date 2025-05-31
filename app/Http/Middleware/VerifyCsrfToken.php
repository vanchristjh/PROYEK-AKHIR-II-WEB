<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */    protected $except = [
        // If you need to exclude any routes from CSRF protection, add them here
        // For example: 'api/*'
    ];

    /**
     * The lifetime of CSRF tokens in minutes.
     * 
     * @var int
     */
    protected $tokensLifetime = 240; // 4 hours in minutes
}
