<?php

namespace App\Enums;

use App\Traits\EnumLabelTrait;

enum TypeEnum: string
{
    use EnumLabelTrait;

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
            self::ABONNEMENTS => '📆',
            self::ACHATS_MAISON => '🏠',
            self::ALIMENTATION_COURSES => '🛒',
            self::ANIMAUX => '🐾',
            self::ASSURANCES => '🛡️',
            self::DIVERS => '🗂️',
            self::DIVERTISSEMENTS_LOISIRS => '🎭',
            self::DONS_CONTRIBUTIONS => '🙏',
            self::EDUCATION => '🎓',
            self::ENFANTS => '👶',
            self::FINANCES => '💰',
            self::IMPOTS_CONTRIBUTIONS => '📋',
            self::LOGEMENT => '🏘️',
            self::SANTE_BIEN_ETRE => '💆',
            self::SERVICES_DOMICILE => '🧹',
            self::TECHNOLOGIE => '💻',
            self::TRANSPORT => '🚗',
            self::VOYAGES => '✈️',
        };
    }

    public function categoryEnums(): array
    {
        return match ($this) {
            self::ABONNEMENTS => [
                CategoryEnum::ABO_ALIMENTAIRE,
                CategoryEnum::ABO_INTERNET_TELECOM,
                CategoryEnum::ABO_JEUX_VIDEO,
                CategoryEnum::ABO_LOGICIELS_PRO,
                CategoryEnum::ABO_MAGAZINES,
                CategoryEnum::ABO_MOBILITE,
                CategoryEnum::ABO_SERVICES_LIGNE,
                CategoryEnum::ABO_SPORT,
                CategoryEnum::ABO_STREAMING_MUSIQUE,
                CategoryEnum::ABO_STREAMING_VIDEO,
                CategoryEnum::ABO_TELEVISION,
                CategoryEnum::ABO_TRANSPORTS,
            ],
            self::ACHATS_MAISON => [
                CategoryEnum::AMENAGEMENT,
                CategoryEnum::DECORATION,
                CategoryEnum::GROS_ELECTROMENAGER,
                CategoryEnum::MEUBLES,
                CategoryEnum::PETIT_ELECTROMENAGER,
                CategoryEnum::SECURITE,
            ],
            self::ALIMENTATION_COURSES => [
                CategoryEnum::LIVRAISON_REPAS,
                CategoryEnum::RESTAURANTS,
                CategoryEnum::SUPERMARCHE,
            ],
            self::ANIMAUX => [
                CategoryEnum::ANIMAUX_NOURRITURE,
                CategoryEnum::ANIMAUX_VETERINAIRE,
                CategoryEnum::ANIMAUX_TOILETTAGE,
                CategoryEnum::ANIMAUX_ACCESSOIRES,
                CategoryEnum::ANIMAUX_PENSION,
                CategoryEnum::ANIMAUX_ASSURANCE,
                CategoryEnum::ANIMAUX_DRESSAGE,
                CategoryEnum::ANIMAUX_ACTIVITES,
            ],
            self::ASSURANCES => [
                CategoryEnum::ASSURANCE_HABITATION,
                CategoryEnum::ASSURANCE_INCENDIE,
                CategoryEnum::ASSURANCE_SANTE,
                CategoryEnum::ASSURANCE_VIE,
                CategoryEnum::ASSURANCE_VOITURE,
            ],
            self::DIVERS => [
                CategoryEnum::AUTRES,
                CategoryEnum::CADEAUX,
            ],
            self::DIVERTISSEMENTS_LOISIRS => [
                CategoryEnum::INSTRUMENTS_MUSIQUE,
                CategoryEnum::LOISIRS_CULTURELS,
                CategoryEnum::LOISIRS_SPORTIFS,
            ],
            self::DONS_CONTRIBUTIONS => [
                CategoryEnum::COTISATIONS_ASSO,
                CategoryEnum::DONS_CARITATIFS,
            ],
            self::EDUCATION => [
                CategoryEnum::ACTIVITES_PARASCOLAIRES,
                CategoryEnum::CANTINE,
                CategoryEnum::FOURNITURES_SCOLAIRES,
                CategoryEnum::FRAIS_SCOLARITE,
                CategoryEnum::LIVRES_SCOLAIRES,
            ],
            self::ENFANTS => [
                CategoryEnum::ACHAT_VETEMENTS,
                CategoryEnum::ARGENT_POCHE,
                CategoryEnum::CRECHE,
            ],
            self::FINANCES => [
                CategoryEnum::CREDIT_PERSONNEL,
                CategoryEnum::FRAIS_BANCAIRES,
                CategoryEnum::FRAIS_NOTAIRE,
                CategoryEnum::PRETS_PERSONNELS,
                CategoryEnum::REVENU_RETRAITE,
            ],
            self::IMPOTS_CONTRIBUTIONS => [
                CategoryEnum::ALLOCATIONS_FAMILIALES,
                CategoryEnum::COTISATIONS_SOCIALES,
                CategoryEnum::IMPOTS_REVENU,
            ],
            self::LOGEMENT => [
                CategoryEnum::CHARGES_LOCATIVES,
                CategoryEnum::CHAUFFAGE,
                CategoryEnum::CREDIT_IMMOBILIER,
                CategoryEnum::EAU,
                CategoryEnum::ELECTRICITE,
                CategoryEnum::GAZ,
                CategoryEnum::HYPOTHEQUE,
                CategoryEnum::LOYER,
                CategoryEnum::TAXE_FONCIERE,
                CategoryEnum::TAXE_HABITATION,
            ],
            self::SANTE_BIEN_ETRE => [
                CategoryEnum::COMPLEMENTS_ALIMENTAIRES,
                CategoryEnum::FRAIS_MEDICAUX,
                CategoryEnum::MEDICAMENTS,
                CategoryEnum::SOINS_PARAMEDICAUX,
                CategoryEnum::THERAPIE,
            ],
            self::SERVICES_DOMICILE => [
                CategoryEnum::JARDINAGE,
                CategoryEnum::NETTOYAGE,
                CategoryEnum::REPARATIONS,
            ],
            self::TECHNOLOGIE => [
                CategoryEnum::ACHAT_ELECTRONIQUE,
                CategoryEnum::JEUX_VIDEOS,
                CategoryEnum::LOGICIELS,
            ],
            self::TRANSPORT => [
                CategoryEnum::BILLETS_TRANSPORT,
                CategoryEnum::ENTRETIEN_AUTO,
                CategoryEnum::ESSENCE,
                CategoryEnum::LOCATION_VOITURE,
                CategoryEnum::REPARATION_AUTO,
                CategoryEnum::STATIONNEMENT,
                CategoryEnum::TRANSPORT_SCOLAIRE,
            ],
            self::VOYAGES => [
                CategoryEnum::ACTIVITES_VACANCES,
                CategoryEnum::HOTEL,
            ],
        };
    }

    public function categories(): array
    {
        return array_map(
            fn (CategoryEnum $category) => $category->value,
            $this->categoryEnums()
        );
    }

    public static function getTypesOptions(): array
    {
        return self::getOptions();
    }

    public static function getTypesOptionsWithEmojis(): array
    {
        return self::getOptionsWithEmojis();
    }
}
