<div class="flex flex-col justify-center max-w-3xl mx-auto my-5 md:my-10">

    <div class="relative p-6">
        <div class="flex justify-between flex-wrap gap-x-6 gap-y-4 mx-4">

            <h2 role="heading" aria-level="2" class="text-lg-medium text-slate-800">
                @if($step === 1)
                    Créez votre famille
                @elseif($step === 2)
                    Invitez des membres
                @elseif($step === 3)
                    Récapitulatif
                @else
                    Félicitations !
                @endif
            </h2>

            <div class="flex items-center">
                <span class="text-sm font-medium text-gray-500 mr-4 md:ml-6">
                    Étape {{ $step }} sur 4
                </span>

                <div class="flex items-center">
                    @for ($i = 1; $i <= 4; $i++)
                        <div class="flex items-center">
                            <div class="relative flex-center w-7 h-7 rounded-full {{ $step === $i ? 'bg-slate-700' : ($step > $i ? 'bg-slate-600' : 'bg-gray-100 border border-gray-200') }}">
                                <span class="text-xs font-medium {{ $step >= $i ? 'text-white' : 'text-gray-500' }}">{{ $i }}</span>
                            </div>

                            @if($i < 4)
                                <div class="w-7 h-0.5 mx-1 {{ $step > $i ? 'bg-slate-400' : 'bg-gray-200' }}"></div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 pt-8 mx-4">
        <!-- Étape 1: Création de la famille -->
        @if($step === 1)
            <div class="space-y-6">
                <div class="text-center mb-8">
                    <h2 role="heading" aria-level="2" class="text-2xl font-bold text-gray-800 mb-3">Bienvenue sur notre plateforme !</h2>
                    <p class="text-gray-600 max-w-md mx-auto">
                        Commençons par créer votre espace famille. C'est là que vous pourrez gérer vos dépenses communes.
                    </p>
                </div>

                <div class="max-w-lg mx-auto md:px-4">
                    <x-form.field
                        label="Nom de votre famille"
                        name="form.familyName"
                        model="form.familyName"
                        placeholder="Exemple: Famille Dupont"
                        :asterix="true"
                        autofocus
                        required
                    />
                    <p class="mt-2 px-2 text-sm text-gray-500">
                        Ce nom sera visible par tous les membres que vous inviterez.
                    </p>
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="nextStep" class="button-secondary">
                        Continuer
                        <x-svg.arrows.right class="text-white" />
                    </button>
                </div>
            </div>
        @endif

        <!-- Étape 2: Invitation des membres -->
        @if($step === 2)
            <div class="space-y-8">
                <div class="text-center mb-8">
                    <h2 role="heading" aria-level="2" class="text-2xl font-bold text-gray-800 mb-3">
                        Invitez des membres à rejoindre votre famille
                    </h2>
                    <p class="text-gray-600 max-w-md mx-auto">
                        Ajoutez des membres pour partager vos dépenses. Vous pourrez toujours en ajouter plus tard.</p>
                </div>

                <div class="space-y-3 my-6 md:px-2">
                    @foreach($members as $index => $member)
                        <div class="relative p-3 pt-6 md:pr-6 rounded-lg bg-gray-50 border border-slate-200"
                             wire:key="member-{{ $index }}">
                            <div class="flex items-start flex-wrap gap-4">
                                <div class="flex flex-col w-full">
                                    <x-form.field
                                        label="Adresse e-mail"
                                        name="members.{{ $index }}.email"
                                        type="email"
                                        model="members.{{ $index }}.email"
                                        placeholder="exemple@gmail.com"
                                        wire:change="validateMemberEmail({{ $index }})"
                                        :asterix="true"
                                        required
                                    />
                                    @if(!empty($members[$index]['error']))
                                        <span class="inline-flex text-red-500 text-sm-medium pl-2 max-w-xs mt-1">{{ $members[$index]['error'] }}</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap md:grid grid-cols-2 gap-4 w-full md:w-auto">
                                    <div class="w-full">
                                        <x-form.select
                                            label="Rôle"
                                            name="members.{{ $index }}.permission"
                                            model="members.{{ $index }}.permission"
                                            required
                                        >
                                            @foreach(App\Enums\FamilyPermissionEnum::cases() as $permission)
                                                <option value="{{ $permission->value }}">{{ $permission->label() }}</option>
                                            @endforeach
                                        </x-form.select>

                                        <p class="py-1.5 px-2.5 text-xs text-gray-500">
                                            {{ App\Enums\FamilyPermissionEnum::tryFrom($members[$index]['permission'] ?? 'viewer')->description() }}
                                        </p>
                                    </div>
                                    <div class="w-full">
                                        <x-form.select
                                            label="Relation"
                                            name="members.{{ $index }}.relation"
                                            model="members.{{ $index }}.relation"
                                            :asterix="true"
                                            required
                                        >
                                            @foreach(App\Enums\FamilyRelationEnum::getRelationOptions() as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </x-form.select>
                                    </div>
                                </div>

                                @if(count($members) > 1)
                                    <button wire:click="removeMember({{ $index }})"
                                            class="absolute top-1.5 right-1.5 p-2 rounded-md hover:bg-red-100 group">
                                        <x-svg.trash class="group-hover:text-red-500"/>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <button wire:click="addMember" class="button-classic">
                        <x-svg.add2/>
                        Ajouter un autre membre à inviter
                    </button>
                </div>

                <div class="flex items-center justify-between flex-wrap gap-3 mt-8">
                    <button wire:click="previousStep" class="button-secondary">
                        <x-svg.arrows.left class="text-white" />
                        Retour
                    </button>
                    <button wire:click="skipInvitations" class="button-primary">
                        Ignorer cette étape
                    </button>
                    <button wire:click="nextStep" class="button-secondary">
                        Continuer
                        <x-svg.arrows.right class="text-white" />
                    </button>
                </div>
            </div>
        @endif

        <!-- Étape 3: Récapitulatif -->
        @if($step === 3)
            <div class="space-y-4 max-w-2xl mx-auto px-2">
                <!-- En-tête avec titre et sous-titre -->
                <div class="text-center mb-8">
                    <h2 role="heading" aria-level="2" class="text-2xl font-bold text-slate-800 mb-3">
                        Récapitulatif de votre famille
                    </h2>
                    <p class="text-gray-500 max-w-md mx-auto">
                        Vérifiez les informations avant de finaliser.
                    </p>
                </div>

                <!-- Informations générales - Design épuré avec espacement optimisé -->
                <div class="pt-5 pb-2 px-6 rounded-lg bg-white border border-slate-200">
                    <h2 role="heading" aria-level="2" class="text-lg-medium text-slate-800 mb-4">
                        Informations générales
                    </h2>

                    <!-- Grille d'informations avec espacement uniforme -->
                    <div class="divide-y divide-gray-100">
                        <div class="flex items-center justify-between py-4">
                            <span class="text-sm text-gray-500">Nom de la famille</span>
                            <span class="text-sm-medium text-slate-800">{{ $form->familyName }}</span>
                        </div>
                        <div class="flex items-center justify-between py-4">
                            <span class="text-sm text-gray-500">Créateur</span>
                            <span class="text-sm-medium text-slate-800">{{ Auth::user()->name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-4">
                            <span class="text-sm text-gray-500">Votre rôle</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs-medium {{ App\Enums\FamilyPermissionEnum::Admin->cssClasses() }}">
                                    {{ App\Enums\FamilyPermissionEnum::Admin->label() }}
                                </span>
                        </div>
                    </div>
                </div>

                <!-- Membres invités - Avec design de carte légère -->
                @if(count(array_filter($members, fn($m) => !empty($m['email']))) > 0)
                    <div class="pt-5 pb-2 px-6 rounded-lg bg-white border border-slate-200">
                        <h2 role="heading" aria-level="2" class="text-lg font-medium text-slate-800 mb-5">Membres invités</h2>
                        <div class="space-y-3">
                            @foreach($members as $member)
                                @if(!empty($member['email']) && $member['valid'])
                                    <div class="flex justify-between items-center py-3 px-1 border-b border-slate-200 last:border-0">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $member['email'] }}</p>
                                            @php
                                                $relation = App\Enums\FamilyRelationEnum::tryFrom($member['relation']);
                                                $relationLabel = $relation ? $relation->label() : ucfirst($member['relation']);
                                            @endphp
                                            <p class="text-sm text-gray-500 mt-1">{{ $relationLabel }}</p>
                                        </div>
                                        <div>
                                            @php
                                                $permission = App\Enums\FamilyPermissionEnum::tryFrom($member['permission']);
                                                $permissionClass = $permission ? $permission->cssClasses() : '';
                                                $permissionLabel = $permission ? $permission->label() : '';
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $permissionClass }}">
                                                    {{ $permissionLabel }}
                                                </span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Notification info - Design plus léger avec icône intégrée -->
                <div class="bg-blue-50 rounded-xl py-3 px-4 flex gap-4 border border-blue-100">
                    <x-svg.info class="relative top-0.5 min-w-3 text-blue-500" />
                    <p class="text-sm text-blue-700">
                        En cliquant sur "Créer ma famille", vous allez créer une famille dont vous serez l'administrateur.
                        @if(count(array_filter($members, fn($m) => !empty($m['email']))) > 0)
                            Des emails d'invitation seront envoyés aux membres que vous avez ajoutés.
                        @endif
                    </p>
                </div>

                <!-- Boutons de navigation - Design épuré et spacing amélioré -->
                <div class="flex justify-between mt-8">
                    <button wire:click="previousStep" class="button-secondary">
                        <x-svg.arrows.left class="text-white" />
                        Retour
                    </button>

                    <button wire:click="submitForm" class="button-secondary" wire:loading.attr="disabled">
                        <x-svg.add2 class="text-white" />
                        {{ __('Créer ma famille') }}
                    </button>
                </div>
            </div>
        @endif

        <!-- Étape 4: Succès -->
        @if($step === 4)
            <div x-data="{
                initConfetti() {
                    if (typeof confetti === 'undefined') {
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js';
                        script.onload = () => this.runConfetti();
                        document.head.appendChild(script);
                    } else {
                        this.runConfetti();
                    }
                },
                runConfetti() {
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 },
                        colors: ['#22c55e', '#10b981', '#3b82f6', '#6366f1', '#f97316']
                    });

                const duration = 3000;
                const end = Date.now() + duration;

                const frame = () => {
                    confetti({
                        particleCount: 2,
                        angle: 60,
                        spread: 55,
                        origin: { x: 0 },
                        colors: ['#22c55e', '#10b981', '#3b82f6', '#6366f1']
                    });

                    confetti({
                            particleCount: 2,
                            angle: 120,
                            spread: 55,
                            origin: { x: 1 },
                            colors: ['#22c55e', '#10b981', '#3b82f6', '#6366f1']
                        });

                        if (Date.now() < end) {
                            requestAnimationFrame(frame);
                        }
                    };

                        frame();

                        setTimeout(() => {
                            confetti({
                                particleCount: 150,
                                angle: 90,
                                spread: 120,
                                origin: { y: 0.5 },
                                gravity: 1,
                                colors: ['#22c55e', '#f97316', '#6366f1', '#10b981', '#3b82f6']
                            });
                        }, 500);
                    }
                }"
                 x-init="initConfetti()"
                 class="flex-center flex-col gap-6 pb-2"
            >
                <!-- Canvas pour les confettis (sera créé par la bibliothèque) -->

                <x-svg.success class="h-12 w-12 text-green-500" />

                <h2 role="heading" aria-level="2" class="text-2xl font-semibold text-slate-800 text-center">
                    Félicitations !
                </h2>

                <p class="text-gray-600 max-w-md text-center">
                    Votre famille <span class="font-medium text-slate-800">{{ $form->familyName }}</span> a été créée avec succès.
                    @if($this->hasInvitedMembers())
                        <span class="block mt-2">Les invitations ont été envoyées aux membres que vous avez ajoutés.</span>
                    @endif
                </p>

                <button wire:click="finishOnboarding" class="button-success">
                    Commencer à utiliser l'application
                    <x-svg.validate class="text-white" />
                </button>
            </div>
        @endif
    </div>
</div>
