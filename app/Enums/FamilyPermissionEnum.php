<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum FamilyPermissionEnum: string
{
    use EnumLabelTrait;

    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrateur',
            self::Editor => 'Éditeur',
            self::Viewer => 'Lecteur',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Contrôle total : peut inviter, modifier, supprimer des membres et gérer tous les aspects de la famille.',
            self::Editor => 'Peut ajouter et modifier le contenu, mais ne peut pas supprimer la famille ou gérer les membres.',
            self::Viewer => 'Accès en lecture seule : peut consulter les informations, mais ne peut rien modifier.',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Admin => 'blue',
            self::Editor => 'amber',
            self::Viewer => 'teal',
        };
    }

    public function cssClasses(): string
    {
        return match ($this) {
            self::Admin => 'bg-blue-100/50 text-blue-700 border-blue-200',
            self::Editor => 'bg-amber-100/50 text-amber-700 border-amber-200',
            self::Viewer => 'bg-teal-100/50 text-teal-700 border-teal-200',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::Admin, self::Editor]);
    }

    public static function getPermissionOptions(): array
    {
        return self::getOptions();
    }
}
