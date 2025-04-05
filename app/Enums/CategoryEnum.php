<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum CategoryEnum: string
{
    use EnumLabelTrait;

    // Abonnements
    case ABO_ALIMENTAIRE = 'Abonnement alimentaire';
    case ABO_INTERNET_TELECOM = 'Abonnement internet & télécommunications';
    case ABO_JEUX_VIDEO = 'Abonnement jeux Vidéo en ligne';
    case ABO_LOGICIELS_PRO = 'Abonnement logiciels professionnels';
    case ABO_MAGAZINES = 'Abonnement magazines/revues';
    case ABO_MOBILITE = 'Abonnement services de mobilité';
    case ABO_SERVICES_LIGNE = 'Abonnement services en ligne';
    case ABO_SPORT = 'Abonnement salles ou clubs de sport';
    case ABO_STREAMING_MUSIQUE = 'Abonnement plateformes de streaming musical';
    case ABO_STREAMING_VIDEO = 'Abonnement plateformes de streaming vidéo';
    case ABO_TELEVISION = 'Abonnement télévision/IPTV';
    case ABO_TRANSPORTS = 'Abonnement transports en commun';

    // Achats de la maison
    case AMENAGEMENT = 'Aménagement intérieur';
    case DECORATION = 'Décoration';
    case GROS_ELECTROMENAGER = 'Gros électroménager';
    case MEUBLES = 'Meubles';
    case PETIT_ELECTROMENAGER = 'Petit électroménager';
    case SECURITE = 'Sécurité';

    // Alimentation et Courses
    case LIVRAISON_REPAS = 'Livraison de repas';
    case RESTAURANTS = 'Restaurants';
    case SUPERMARCHE = 'Supermarché';

    // Animaux
    case ANIMAUX_NOURRITURE = 'Nourriture';
    case ANIMAUX_VETERINAIRE = 'Vétérinaire';
    case ANIMAUX_TOILETTAGE = 'Toilettage';
    case ANIMAUX_ACCESSOIRES = 'Accessoires';
    case ANIMAUX_PENSION = 'Pension';
    case ANIMAUX_ASSURANCE = 'Assurance';
    case ANIMAUX_DRESSAGE = 'Dressage';
    case ANIMAUX_ACTIVITES = 'Activités';

    // Divers
    case AUTRES = 'Autres';
    case CADEAUX = 'Cadeaux d\'Anniversaire';

    // Divertissements et Loisirs
    case INSTRUMENTS_MUSIQUE = 'Instruments de musique';
    case LOISIRS_CULTURELS = 'Loisirs culturels';
    case LOISIRS_SPORTIFS = 'Loisirs sportifs';

    // Dons et Contributions
    case COTISATIONS_ASSO = 'Cotisations associatives';
    case DONS_CARITATIFS = 'Dons caritatifs';

    // Éducation
    case ACTIVITES_PARASCOLAIRES = 'Activités parascolaires/extrascolaires';
    case CANTINE = 'Repas/cantine scolaire';
    case FOURNITURES_SCOLAIRES = 'Fournitures scolaires';
    case FRAIS_SCOLARITE = 'Frais de scolarité';
    case LIVRES_SCOLAIRES = 'Livres scolaires';

    // Enfants
    case ACHAT_VETEMENTS = 'Achat de vêtements';
    case ARGENT_POCHE = 'Argent de poche';
    case CRECHE = 'Crèche ou babysitting';

    // Finances
    case CREDIT_PERSONNEL = 'Crédit personnel';
    case FRAIS_BANCAIRES = 'Frais bancaires, cartes de crédit';
    case FRAIS_NOTAIRE = 'Frais de notaire ou de contrat';
    case PRETS_PERSONNELS = 'Prêts personnels';
    case REVENU_RETRAITE = 'Revenu de Retraite';

    // Impôts et Contributions Sociales
    case ALLOCATIONS_FAMILIALES = 'Allocations familiales';
    case COTISATIONS_SOCIALES = 'Cotisations sociales';
    case IMPOTS_REVENU = 'Impôts sur le revenu';

    // Logement
    case CHARGES_LOCATIVES = 'Charges locatives';
    case CHAUFFAGE = 'Chauffage central';
    case CREDIT_IMMOBILIER = 'Crédit immobilier';
    case EAU = 'Eau';
    case ELECTRICITE = 'Électricité';
    case GAZ = 'Gaz';
    case HYPOTHEQUE = 'Hypothèque';
    case LOYER = 'Loyer';
    case TAXE_FONCIERE = 'Taxe foncière';
    case TAXE_HABITATION = 'Taxe d\'habitation';

    // Santé et Bien-être
    case COMPLEMENTS_ALIMENTAIRES = 'Compléments alimentaires';
    case FRAIS_MEDICAUX = 'Frais médicaux';
    case MEDICAMENTS = 'Médicaments';
    case SOINS_PARAMEDICAUX = 'Soins paramédicaux';
    case THERAPIE = 'Thérapie ou coaching';

    // Services à domicile
    case JARDINAGE = 'Jardinage';
    case NETTOYAGE = 'Nettoyage';
    case REPARATIONS = 'Réparations';

    // Technologie
    case ACHAT_ELECTRONIQUE = 'Achat d\'électronique';
    case JEUX_VIDEOS = 'Jeux vidéos';
    case LOGICIELS = 'Logiciels';

    // Transport
    case BILLETS_TRANSPORT = 'Billets de transport';
    case ENTRETIEN_AUTO = 'Entretien automobile';
    case ESSENCE = 'Essence';
    case LOCATION_VOITURE = 'Location de voiture';
    case REPARATION_AUTO = 'Réparation automobile';
    case STATIONNEMENT = 'Frais de stationnement';
    case TRANSPORT_SCOLAIRE = 'Transport scolaire';

    // Voyages
    case ACTIVITES_VACANCES = 'Activités de vacances';
    case HOTEL = 'Réservation d\'hôtel';

    // Assurances
    case ASSURANCE_HABITATION = 'Assurance habitation';
    case ASSURANCE_INCENDIE = 'Assurance incendie';
    case ASSURANCE_SANTE = 'Assurance santé';
    case ASSURANCE_VIE = 'Assurance vie';
    case ASSURANCE_VOITURE = 'Assurance voiture/moto';

    public function emoji(): string
    {
        return match ($this) {
            // Abonnements
            self::ABO_ALIMENTAIRE => '🍱',
            self::ABO_INTERNET_TELECOM => '📡',
            self::ABO_JEUX_VIDEO => '🎮',
            self::ABO_LOGICIELS_PRO => '💼',
            self::ABO_MAGAZINES => '📰',
            self::ABO_MOBILITE => '🛴',
            self::ABO_SERVICES_LIGNE => '🌐',
            self::ABO_SPORT => '🏋️',
            self::ABO_STREAMING_MUSIQUE => '🎵',
            self::ABO_STREAMING_VIDEO => '📺',
            self::ABO_TELEVISION => '📡',
            self::ABO_TRANSPORTS => '🚌',

            // Achats de la maison
            self::AMENAGEMENT => '🏗️',
            self::DECORATION => '🎨',
            self::GROS_ELECTROMENAGER => '🧊',
            self::MEUBLES => '🪑',
            self::PETIT_ELECTROMENAGER => '🔌',
            self::SECURITE => '🔒',

            // Alimentation et Courses
            self::LIVRAISON_REPAS => '🛵',
            self::RESTAURANTS => '🍽️',
            self::SUPERMARCHE => '🛒',

            // Animaux
            self::ANIMAUX_NOURRITURE => '🥩',
            self::ANIMAUX_VETERINAIRE => '🏥',
            self::ANIMAUX_TOILETTAGE => '🧼',
            self::ANIMAUX_ACCESSOIRES => '🧶',
            self::ANIMAUX_PENSION => '🏠',
            self::ANIMAUX_ASSURANCE => '📝',
            self::ANIMAUX_DRESSAGE => '🦮',
            self::ANIMAUX_ACTIVITES => '🐕',

            // Divers
            self::AUTRES => '📦',
            self::CADEAUX => '🎁',

            // Divertissements et Loisirs
            self::INSTRUMENTS_MUSIQUE => '🎸',
            self::LOISIRS_CULTURELS => '🎭',
            self::LOISIRS_SPORTIFS => '⚽',

            // Dons et Contributions
            self::COTISATIONS_ASSO => '🤝',
            self::DONS_CARITATIFS => '❤️',

            // Éducation
            self::ACTIVITES_PARASCOLAIRES => '🎯',
            self::CANTINE => '🍲',
            self::FOURNITURES_SCOLAIRES => '✏️',
            self::FRAIS_SCOLARITE => '🏫',
            self::LIVRES_SCOLAIRES => '📚',

            // Enfants
            self::ACHAT_VETEMENTS => '👕',
            self::ARGENT_POCHE => '💸',
            self::CRECHE => '👶',

            // Finances
            self::CREDIT_PERSONNEL => '💳',
            self::FRAIS_BANCAIRES => '🏦',
            self::FRAIS_NOTAIRE => '📝',
            self::PRETS_PERSONNELS => '💰',
            self::REVENU_RETRAITE => '👴',

            // Impôts et Contributions Sociales
            self::ALLOCATIONS_FAMILIALES => '👨‍👩‍👧‍👦',
            self::COTISATIONS_SOCIALES => '📊',
            self::IMPOTS_REVENU => '💸',

            // Logement
            self::CHARGES_LOCATIVES => '🏢',
            self::CHAUFFAGE => '🔥',
            self::CREDIT_IMMOBILIER => '🏠',
            self::EAU => '💧',
            self::ELECTRICITE => '⚡',
            self::GAZ => '🔥',
            self::HYPOTHEQUE => '📄',
            self::LOYER => '🔑',
            self::TAXE_FONCIERE => '📋',
            self::TAXE_HABITATION => '🏘️',

            // Santé et Bien-être
            self::COMPLEMENTS_ALIMENTAIRES => '💊',
            self::FRAIS_MEDICAUX => '🩺',
            self::MEDICAMENTS => '💊',
            self::SOINS_PARAMEDICAUX => '👨‍⚕️',
            self::THERAPIE => '🧠',

            // Services à domicile
            self::JARDINAGE => '🌱',
            self::NETTOYAGE => '🧹',
            self::REPARATIONS => '🔧',

            // Technologie
            self::ACHAT_ELECTRONIQUE => '📱',
            self::JEUX_VIDEOS => '🎮',
            self::LOGICIELS => '💻',

            // Transport
            self::BILLETS_TRANSPORT => '🎫',
            self::ENTRETIEN_AUTO => '🔧',
            self::ESSENCE => '⛽',
            self::LOCATION_VOITURE => '🚗',
            self::REPARATION_AUTO => '🔨',
            self::STATIONNEMENT => '🅿️',
            self::TRANSPORT_SCOLAIRE => '🚌',

            // Voyages
            self::ACTIVITES_VACANCES => '🏄',
            self::HOTEL => '🏨',

            // Assurances
            self::ASSURANCE_HABITATION => '🏠',
            self::ASSURANCE_INCENDIE => '🔥',
            self::ASSURANCE_SANTE => '🏥',
            self::ASSURANCE_VIE => '📜',
            self::ASSURANCE_VOITURE => '🚗',
        };
    }

    public function getType(): TypeEnum
    {
        foreach (TypeEnum::cases() as $type) {
            // Check if the current enum is in the category enums of the type
            if (in_array($this, $type->categoryEnums())) {
                return $type;
            }
        }

        // Default
        return TypeEnum::DIVERS;
    }

    public static function getCategoryOptions(): array
    {
        return self::getOptions();
    }

    public static function getCategoryOptionsWithEmojis(): array
    {
        return self::getOptionsWithEmojis();
    }

    public static function getCategoriesForType(TypeEnum $type): array
    {
        $categories = [];
        foreach ($type->categoryEnums() as $categoryEnum) {
            $categories[$categoryEnum->value] = $categoryEnum->value;
        }

        return $categories;
    }

    public static function getCategoriesForTypeWithEmojis(TypeEnum $type): array
    {
        $categories = [];
        foreach ($type->categoryEnums() as $categoryEnum) {
            $categories[$categoryEnum->value] = $categoryEnum->labelWithEmoji();
        }

        return $categories;
    }
}
