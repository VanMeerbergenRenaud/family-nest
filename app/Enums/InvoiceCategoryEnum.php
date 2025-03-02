<?php

namespace App\Enums;

enum InvoiceCategoryEnum: string
{
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

    // Divers
    case ANIMAUX = 'Animaux';
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

    /**
     * Récupérer le type associé à cette catégorie
     */
    public function getType(): InvoiceTypeEnum
    {
        foreach (InvoiceTypeEnum::cases() as $type) {
            if (in_array($this->value, $type->categories())) {
                return $type;
            }
        }

        // Par défaut, retourner Divers
        return InvoiceTypeEnum::DIVERS;
    }
}
