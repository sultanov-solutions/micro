<?php

namespace SultanovSolutions\Micro\Core\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticateWithBasicAuth
{
    /**
     * The guard factory instance.
     *
     * @var AuthFactory
     */
    protected AuthFactory $auth;

    /**
     * Create a new middleware instance.
     *
     * @param AuthFactory $auth
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $guard
     * @param string|null $field
     * @return mixed
     *
     * @throws UnauthorizedHttpException
     */
    public function handle(Request $request, Closure $next, string $guard = null, string $field = null)
    {
        $this->auth->guard($guard)->basic($field ?: 'email');

        return $next($request);
    }
}
