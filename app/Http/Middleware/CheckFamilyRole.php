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
    public function __construct(
        protected FamilyRoleService $roleService
    ) {}

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $family = $user->family();

        if (! $family) {
            if ($request->routeIs('family')) {
                return $next($request);
            }

            return redirect()->route('family');
        }

        // If the route contains an invoice, check if the user has access to it
        if ($request->route('invoice')) {
            $invoice = $request->route('invoice');
            $invoiceFamily = $invoice->family;

            // If invoice has a family, check that user belongs to that family
            if ($invoiceFamily && ! $user->belongsToFamily($invoiceFamily->id)) {
                return redirect()->route('invoices.index');
            }
        }

        if (! $this->userHasRequiredRole($user, $family, $roles)) {
            return redirect()->route('family');
        }

        return $next($request);
    }

    private function userHasRequiredRole($user, $family, array $roles): bool
    {
        if ($this->roleService->isAdmin($user, $family)) {
            return true;
        }

        foreach ($roles as $role) {
            $permissionEnum = FamilyPermissionEnum::tryFrom($role);

            if (! $permissionEnum) {
                continue;
            }

            $hasRole = match ($permissionEnum) {
                FamilyPermissionEnum::Admin => $this->roleService->isAdmin($user, $family),
                FamilyPermissionEnum::Editor => $this->roleService->isEditorOrAbove($user, $family),
                FamilyPermissionEnum::Viewer => $this->roleService->isViewer($user, $family),
            };

            if ($hasRole) {
                return true;
            }
        }

        return false;
    }
}
