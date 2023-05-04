<?php

namespace App\Http\Middleware;

use App\Http\Repositories\ManagementTokenRepository;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ModeratorAccessMiddleware
{
    public function __construct(private ManagementTokenRepository $managementTokenRepository)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $data = $this->managementTokenRepository->get($request->bearerToken());
        if (! in_array(data_get($data, 'role'), [Role::ROLE_ADMIN, Role::ROLE_MODERATOR])) {
            throw new HttpException(401);
        }

        if (!data_get($data, 'user.email_is_confirmed', false)) {
            throw new HttpException(401);
        }

        return $next($request);
    }
}
