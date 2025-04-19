<?php

namespace App\Http\Middleware;

use App\Enums\FamilyPermissionEnum;
use App\Services\FamilyRoleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFamilyRole
{
    protected FamilyRoleService $roleService;

    public function __construct(FamilyRoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Handle incoming requests and check user's roles.
     *
     * @param  Request  $request  The incoming request.
     * @param  Closure  $next  The next middleware in the pipeline.
     * @param  string  ...$roles  The required roles for accessing the route.
     * @return Response The response to the request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Rediriger l'utilisateur non authentifié vers la page de connexion
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $family = $user->family();

        // Traitement utilisateur sans famille
        if (! $family) {
            if ($request->routeIs('family')) {
                return $next($request);
            }

            return redirect()->route('family');
        }

        // Vérification des rôles
        $hasRequiredRole = $this->checkUserRoles($user, $family, $roles);

        if (! $hasRequiredRole) {
            return redirect()->route('family');
        }

        return $next($request);
    }

    private function checkUserRoles($user, $family, array $roles): bool
    {
        foreach ($roles as $role) {
            $permissionEnum = FamilyPermissionEnum::tryFrom($role);

            if (! $permissionEnum) {
                continue;
            }

            $hasRole = match ($permissionEnum) {
                FamilyPermissionEnum::Admin => $this->roleService->isAdmin($user, $family),
                FamilyPermissionEnum::Editor => $this->roleService->hasRole($user, $permissionEnum->value, $family),
                FamilyPermissionEnum::Viewer => $this->roleService->isViewer($user, $family),
            };

            if ($hasRole) {
                return true;
            }
        }

        return false;
    }
}
