<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum FamilyRelationEnum: string
{
    use EnumLabelTrait;

    case Member = 'member';
    case Spouse = 'spouse';
    case Grandparent = 'grandparent';
    case Parent = 'parent';
    case Child = 'child';
    case Brother = 'brother';
    case Sister = 'sister';
    case Friend = 'friend';
    case Roommate = 'roommate';
    case Coworker = 'coworker';
    case Self = 'self';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Member => 'Membre',
            self::Spouse => 'Conjoint(e)',
            self::Grandparent => 'Grand-parent',
            self::Parent => 'Parent',
            self::Child => 'Enfant',
            self::Brother => 'Frère',
            self::Sister => 'Sœur',
            self::Friend => 'Ami(e)',
            self::Coworker => 'Collègue',
            self::Roommate => 'Colocataire',
            self::Self => 'Créateur',
            self::Other => 'Autre',
        };
    }

    public static function getRelationOptions(): array
    {
        return self::getOptions();
    }
}
