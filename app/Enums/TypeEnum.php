<?php

namespace App\Enums;

enum TypeEnum: string
{
    case ABONNEMENTS = 'Abonnements';
    case ACHATS_MAISON = 'Achats de la maison';
    case ALIMENTATION_COURSES = 'Alimentation et Courses';
    case ANIMAUX = 'Animaux';
    case ASSURANCES = 'Assurances';
    case DIVERS = 'Divers';
    case DIVERTISSEMENTS_LOISIRS = 'Divertissements et Loisirs';
    case DONS_CONTRIBUTIONS = 'Dons et Contributions';
    case EDUCATION = 'Éducation';
    case ENFANTS = 'Enfants';
    case FINANCES = 'Finances';
    case IMPOTS_CONTRIBUTIONS = 'Impôts et Contributions Sociales';
    case LOGEMENT = 'Logement';
    case SANTE_BIEN_ETRE = 'Santé et Bien-être';
    case SERVICES_DOMICILE = 'Services à domicile';
    case TECHNOLOGIE = 'Technologie';
    case TRANSPORT = 'Transport';
    case VOYAGES = 'Voyages';

    public function emoji(): string
    {
        return match ($this) {
            self::ABONNEMENTS => '🔄',
            self::ACHATS_MAISON => '🏠',
            self::ALIMENTATION_COURSES => '🛒',
            self::ANIMAUX => '🐾',
            self::ASSURANCES => '🔐',
            self::DIVERS => '📦',
            self::DIVERTISSEMENTS_LOISIRS => '🎭',
            self::DONS_CONTRIBUTIONS => '🤝',
            self::EDUCATION => '🎓',
            self::ENFANTS => '👶',
            self::FINANCES => '💰',
            self::IMPOTS_CONTRIBUTIONS => '📊',
            self::LOGEMENT => '🏘️',
            self::SANTE_BIEN_ETRE => '⚕️',
            self::SERVICES_DOMICILE => '🧹',
            self::TECHNOLOGIE => '💻',
            self::TRANSPORT => '🚗',
            self::VOYAGES => '✈️',
        };
    }

    public function labelWithEmoji(): string
    {
        return $this->emoji().'&nbsp;&nbsp;'.$this->value;
    }

    public function categories(): array
    {
        return match ($this) {
            self::ABONNEMENTS => [
                'Abonnement alimentaire',
                'Abonnement internet & télécommunications',
                'Abonnement jeux Vidéo en ligne',
                'Abonnement logiciels professionnels',
                'Abonnement magazines/revues',
                'Abonnement plateformes de streaming musical',
                'Abonnement plateformes de streaming vidéo',
                'Abonnement salles ou clubs de sport',
                'Abonnement services de mobilité',
                'Abonnement services en ligne',
                'Abonnement télévision/IPTV',
                'Abonnement transports en commun',
            ],
            self::ACHATS_MAISON => [
                'Aménagement intérieur',
                'Décoration',
                'Gros électroménager',
                'Meubles',
                'Petit électroménager',
                'Sécurité',
            ],
            self::ALIMENTATION_COURSES => [
                'Livraison de repas',
                'Restaurants',
                'Supermarché',
            ],
            self::ANIMAUX => [
                'Nourriture',
                'Vétérinaire',
                'Toilettage',
                'Accessoires',
                'Pension',
                'Assurance',
                'Dressage',
                'Activités',
            ],
            self::ASSURANCES => [
                'Assurance habitation',
                'Assurance incendie',
                'Assurance santé',
                'Assurance vie',
                'Assurance voiture/moto',
            ],
            self::DIVERS => [
                'Autres',
                'Cadeaux d\'Anniversaire',
            ],
            self::DIVERTISSEMENTS_LOISIRS => [
                'Instruments de musique',
                'Loisirs culturels',
                'Loisirs sportifs',
            ],
            self::DONS_CONTRIBUTIONS => [
                'Cotisations associatives',
                'Dons caritatifs',
            ],
            self::EDUCATION => [
                'Activités parascolaires/extrascolaires',
                'Fournitures scolaires',
                'Frais de scolarité',
                'Livres scolaires',
                'Repas/cantine scolaire',
            ],
            self::ENFANTS => [
                'Achat de vêtements',
                'Argent de poche',
                'Crèche ou babysitting',
            ],
            self::FINANCES => [
                'Crédit personnel',
                'Frais bancaires, cartes de crédit',
                'Frais de notaire ou de contrat',
                'Prêts personnels',
                'Revenu de Retraite',
            ],
            self::IMPOTS_CONTRIBUTIONS => [
                'Allocations familiales',
                'Cotisations sociales',
                'Impôts sur le revenu',
            ],
            self::LOGEMENT => [
                'Charges locatives',
                'Chauffage central',
                'Crédit immobilier',
                'Eau',
                'Électricité',
                'Gaz',
                'Hypothèque',
                'Loyer',
                'Taxe d\'habitation',
                'Taxe foncière',
            ],
            self::SANTE_BIEN_ETRE => [
                'Compléments alimentaires',
                'Frais médicaux',
                'Médicaments',
                'Soins paramédicaux',
                'Thérapie ou coaching',
            ],
            self::SERVICES_DOMICILE => [
                'Jardinage',
                'Nettoyage',
                'Réparations',
            ],
            self::TECHNOLOGIE => [
                'Achat d\'électronique',
                'Jeux vidéos',
                'Logiciels',
            ],
            self::TRANSPORT => [
                'Billets de transport',
                'Entretien automobile',
                'Essence',
                'Frais de stationnement',
                'Location de voiture',
                'Réparation automobile',
                'Transport scolaire',
            ],
            self::VOYAGES => [
                'Activités de vacances',
                'Réservation d\'hôtel',
            ],
        };
    }

    public static function getTypesOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->value;
        }

        return $options;
    }

    public static function getTypesOptionsWithEmojis(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->labelWithEmoji();
        }

        return $options;
    }
}
