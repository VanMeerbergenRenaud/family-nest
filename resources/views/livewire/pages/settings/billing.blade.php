<div class="py-2">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 sm:gap-0 mb-6 px-4">
        <div>
            <h2 role="heading" aria-level="2" class="text-xl font-medium text-gray-800">Sélectionnez votre formule</h2>
            <p class="text-sm text-gray-600 mt-1">Prix transparents et sans engagement</p>
        </div>

        <x-radio-group
            wire:model.live="billingCycle"
            class="flex p-1 bg-gray-100 rounded-full border border-slate-200"
        >
            <x-radio-group.option
                value="monthly"
                class="relative px-4 py-2 text-sm-medium rounded-full cursor-pointer overflow-hidden"
                class-checked="bg-white text-gray-800 ring-1 ring-gray-100 hover:ring-gray-200"
                class-not-checked="text-gray-500 hover:text-gray-800"
                x-transition:enter="transition ease-out"
            >
                <span class="text-sm">Mensuel</span>
            </x-radio-group.option>

            <x-radio-group.option
                value="annual"
                class="relative px-4 py-2 ml-1 text-sm-medium rounded-full cursor-pointer overflow-hidden"
                class-checked="bg-white text-gray-800 ring-1 ring-gray-100 hover:ring-gray-200"
                class-not-checked="text-gray-500 hover:text-gray-800"
                x-transition:enter="transition ease-out"
            >
                <div class="flex items-center relative">
                    <span class="text-sm">Annuel</span>

                    <div x-show="$wire.billingCycle === 'annual'" class="relative -right-2 flex-center h-5 rounded-full px-2 text-xs-medium bg-teal-600 text-white">
                        -25%
                    </div>
                </div>
            </x-radio-group.option>
        </x-radio-group>
    </div>

    <!-- Bannière d'essai gratuit -->
    @if(!$isAnnual)
        <div class="bg-white lg:mx-2 rounded-xl border border-slate-200 p-4 mb-6 flex flex-col md:flex-row justify-between items-center gap-3">
            <div>
                <h3 role="heading" aria-level="3" class="text-base font-medium text-gray-800">
                    Essai gratuit de 1 mois disponible
                    <span class="relative -top-0.5 ml-1.5 bg-teal-800 text-white text-xs px-1.5 py-0.5 rounded">
                        ÉCONOMISEZ 15%
                    </span>
                </h3>
                <p class="text-sm text-gray-600 mt-0.5">
                    Passez à un abonnement annuel aujourd'hui et obtenez les 2 premiers mois gratuits.
                </p>
            </div>
            <button wire:click="setBilling('annual')"
                    type="button"
                    class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                Passer à l'annuel
            </button>
        </div>
    @endif

    <!-- Sélection du plan par radio-group -->
    <x-radio-group
        wire:model.live="selectedPlan"
        class="grid grid-cols-1 md:grid-cols-3 gap-4 lg:mx-2"
    >
        @php
            $allFeatures = [
                // Groupe 1: Gestion des factures
                'Gestion des factures' => [
                    'title' => true,
                ],
                'Stockage de factures' => [
                    'Essentiel' => '50 factures',
                    'Famille' => 'Illimité',
                    'Expert' => 'Illimité',
                    'highlight' => ['Famille', 'Expert']
                ],
                'Organisation des factures' => [
                    'Essentiel' => 'Catégories basiques',
                    'Famille' => 'Par événements',
                    'Expert' => 'Par thèmes & objectifs',
                    'highlight' => ['Expert']
                ],
                'Importation de factures' => [
                    'Essentiel' => 'Manuelle uniquement',
                    'Famille' => 'Reconnaissance OCR',
                    'Expert' => 'OCR + IA prédictive',
                    'highlight' => ['Famille', 'Expert']
                ],

                // Groupe 2: Analyse et prévisions
                'Analyse et prévisions' => [
                    'title' => true,
                ],
                'Analyse des dépenses' => [
                    'Essentiel' => 'Vue mensuelle simple',
                    'Famille' => 'Vue complète détaillée',
                    'Expert' => 'Analyse et prédiction',
                    'highlight' => ['Expert']
                ],
                'Moteur de recherche' => [
                    'Essentiel' => 'Recherche simple',
                    'Famille' => 'Recherche avancée',
                    'Expert' => 'Recherche avancée',
                    'highlight' => ['Famille', 'Expert']
                ],
                'Prévisions financières' => [
                    'Essentiel' => false,
                    'Famille' => 'Sur 3 mois',
                    'Expert' => 'Sur 12 mois',
                    'highlight' => ['Famille', 'Expert']
                ],

                // Groupe 3: Collaboration
                'Collaboration' => [
                    'title' => true,
                ],
                'Membres autorisés' => [
                    'Essentiel' => '1 membre',
                    'Famille' => 'Jusqu\'à 5 membres',
                    'Expert' => 'Membres illimités',
                    'highlight' => ['Famille', 'Expert']
                ],
                'Système de rappels' => [
                    'Essentiel' => 'Rappels basiques',
                    'Famille' => 'Personnalisables',
                    'Expert' => 'Intelligents & auto',
                    'highlight' => ['Expert']
                ],
                'Exportation des données' => [
                    'Essentiel' => 'ZIP',
                    'Famille' => 'ZIP et Excel',
                    'Expert' => 'ZIP et Excel',
                    'highlight' => ['Famille', 'Expert']
                ],

                // Groupe 4: Fonctionnalités avancées
                'Fonctionnalités avancées' => [
                    'title' => true,
                ],
                'Tableaux de bord' => [
                    'Essentiel' => 'Basique',
                    'Famille' => 'Standard',
                    'Expert' => 'Personnalisables',
                    'highlight' => ['Expert']
                ],
                'Objectifs financiers' => [
                    'Essentiel' => false,
                    'Famille' => false,
                    'Expert' => 'Avec gamification',
                    'highlight' => ['Expert']
                ],
                'Support client' => [
                    'Essentiel' => 'Email',
                    'Famille' => 'Email et chat',
                    'Expert' => 'Prioritaire 7j/7',
                    'highlight' => ['Expert']
                ]
            ];

            $plans = [
                [
                    'name' => 'Essentiel',
                    'description' => 'Pour démarrer simplement',
                    'price' => '0€',
                    'button' => [
                        'text' => 'Plan gratuit par défaut',
                        'class' => 'button-primary disabled cursor-not-allowed',
                    ],
                    'tag' => null
                ],
                [
                    'name' => 'Famille',
                    'description' => 'Solution complète pour les familles',
                    'price' => $isAnnual ? '9,99€' : '12,99€',
                    'button' => [
                        'text' => 'Améliorer le plan',
                        'class' => 'button-secondary bg-teal-600 hover:bg-teal-700',
                    ],
                    'tag' => 'le plus populaire'
                ],
                [
                    'name' => 'Expert',
                    'description' => 'Gestion financière avancée',
                    'price' => $isAnnual ? '18,99€' : '21,99€',
                    'button' => [
                        'text' => 'Améliorer le plan',
                        'class' => 'button-secondary bg-teal-600 hover:bg-teal-700',
                    ],
                    'tag' => 'le plus complet'
                ]
            ];
        @endphp

        @foreach ($plans as $plan)
            <x-radio-group.option
                value="{{ $plan['name'] }}"
                class="bg-white rounded-xl border border-slate-200 overflow-hidden cursor-pointer"
                class-checked="ring-2 ring-teal-500"
                class-not-checked=""
            >
                <div class="relative p-6 pb-5 border-b border-slate-200"
                     :class="{ 'bg-teal-50': $wire.selectedPlan === '{{ $plan['name'] }}' }"
                >
                    @if ($plan['tag'])
                        <div class="flex justify-between items-start mb-1">
                            <h3 role="heading" aria-level="3" class="text-lg font-medium text-gray-800">{{ $plan['name'] }}</h3>
                            <span class="absolute top-3 right-3 text-xs-medium bg-teal-100 text-teal-800 px-3 py-1 rounded-full uppercase">{{ $plan['tag'] }}</span>
                        </div>
                    @else
                        <h3 role="heading" aria-level="3" class="text-lg font-medium text-gray-800 mb-1">{{ $plan['name'] }}</h3>
                    @endif
                    <p class="text-md text-gray-600 mb-5">{{ $plan['description'] }}</p>

                    <div class="flex items-baseline mb-4">
                        <span class="text-2xl font-bold text-gray-900">{{ $plan['price'] }}</span>
                        <span class="text-md text-gray-500 ml-1">/ mois</span>
                    </div>

                    <button
                        wire:click="selectPlan('{{ $plan['name'] }}')"
                        type="button"
                        class="{{ $plan['button']['class'] }} w-full justify-center"
                    >
                        <span x-show="$wire.selectedPlan !== '{{ $plan['name'] }}'">{{ $plan['button']['text'] }}</span>
                        <span x-show="$wire.selectedPlan === '{{ $plan['name'] }}'">Choisir ce plan</span>
                    </button>
                </div>

                <!-- Liste des fonctionnalités -->
                <ul class="px-6 py-4 space-y-3">
                    @foreach ($allFeatures as $featureName => $featureProps)
                        @if(isset($featureProps['title']) && $featureProps['title'])
                            <li class="pt-3 pb-1 border-t border-gray-100 first:border-t-0 first:pt-0" wire:key="feature-title-{{ $plan['name'] }}-{{ $featureName }}">
                                <h4 role="heading" aria-level="4" class="font-medium text-gray-800">{{ $featureName }}</h4>
                            </li>
                        @else
                            <li class="flex items-start" wire:key="feature-{{ $plan['name'] }}-{{ $featureName }}">
                                @if($featureProps[$plan['name']] === false)
                                    <x-svg.cross class="h-5 min-w-5 text-gray-400 mt-0.5 mr-2"/>
                                    <span class="text-gray-500">{{ $featureName }}</span>
                                @else
                                    <x-svg.success class="h-5 min-w-5 text-teal-600 mt-0.5 mr-2"/>
                                    <div class="text-gray-600 flex items-center flex-wrap gap-1">
                                        <span>{{ $featureName }}</span>
                                        @if(!is_bool($featureProps[$plan['name']]))
                                            <span class="text-sm text-gray-500">({{ $featureProps[$plan['name']] }})</span>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            </x-radio-group.option>
        @endforeach
    </x-radio-group>
</div>
