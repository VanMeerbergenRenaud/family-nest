<div class="mx-auto my-5 md:my-10 p-4">

    @if($showWelcome)
        <!-- Étape 0 : Accueil -->
        <div class="bg-white rounded-2xl border border-slate-200 max-w-4xl w-full grid md:grid-cols-2 lg:grid-cols-7 overflow-hidden">
            <div class="p-6 lg:p-12 lg:pb-10 flex flex-col lg:col-span-3">
                <h2 role="heading" aria-level="2" class="mt-4 text-3xl font-bold text-gray-800">
                    Bienvenue sur FamilyNest
                </h2>
                <p class="mt-6 text-gray-600 leading-relaxed">
                    Nous sommes ravis de vous compter parmi nous pour construire tout ce que vous voulez en utilisant des factures.
                </p>
                <p class="mt-6 text-gray-600 leading-relaxed">
                    C'est l'idée qui a surgit de mon esprit et ce n'est qu'un début. Nous partagerons davantage avec vous au fil du temps. À bientôt !
                </p>
                <div class="mt-6 flex gap-1.5 items-center">
                    <p class="text-sm-medium text-gray-700">Équipe FamilyNest</p>
                    <x-svg.three-stars class="h-5 w-5 text-gray-800" />
                </div>
                <button type="button" wire:click="firstStep" class="mt-6 lg:mt-10 button-tertiary px-5 rounded-lg w-fit">
                    Commencer
                </button>
            </div>
            <div class="relative flex-center lg:col-span-4">
                <img
                    src="{{ asset('img/onboarding-state.png') }}"
                    alt="Illustration avec un message de bienvenue"
                    class="w-[90%] rounded-tl-xl relative lg:absolute bottom-0 right-0 border-t border-l border-slate-200"
                >
            </div>
        </div>
    @else
        @switch($step)

            @case(1)
                <x-onboarding.step-layout
                    step="1" title="Créez votre famille"
                    description="Commençez par créer votre espace famille. C'est là que vous pourrez gérer vos dépenses communes."
                    layout="split"
                >
                    <div class="flex flex-col gap-2">
                        <x-form.field
                            label="Nom de votre nouvelle famille"
                            name="form.familyName"
                            model="form.familyName"
                            placeholder="Exemple: Famille Janssens"
                            :asterix="true"
                            autofocus
                            required
                            class="capitalize"
                        />
                        <p class="mt-4 text-sm text-slate-500 pl-1">
                            Ce nom sera visible par tous les membres que vous inviterez.
                        </p>
                    </div>

                    {{-- Boutons de navigation --}}
                    <x-slot:footer>
                        <button type="button" wire:click="nextStep" class="button-tertiary">
                            Continuer
                            <x-svg.arrows.right class="text-white"/>
                        </button>
                    </x-slot:footer>
                </x-onboarding.step-layout>
                @break

            @case(2)
                <x-onboarding.step-layout
                    step="2" title="Invitez des membres"
                    description="Ajoutez des membres pour partager vos dépenses. Vous pourrez toujours en ajouter plus tard."
                >
                    <div class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4 md:px-1">
                            @foreach($members as $index => $member)
                                <div class="relative p-3 pt-6 md:pr-6 rounded-lg bg-gray-50 border border-slate-200"
                                     wire:key="member-{{ $index }}">
                                    <div class="flex items-start flex-wrap gap-4">
                                        <div class="w-full">
                                            <x-form.field
                                                label="Adresse mail"
                                                type="email"
                                                name="members.{{ $index }}.email"
                                                model="members.{{ $index }}.email"
                                                placeholder="exemple@gmail.com"
                                                wire:change="validateMemberEmail({{ $index }})"
                                                :asterix="true"
                                                required
                                                class="lowercase"
                                            />
                                            @if(!empty($members[$index]['error']))
                                                <span class="inline-flex text-red-500 text-sm-medium pl-2 max-w-xs mt-1">
                                                    {{ $members[$index]['error'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex flex-wrap gap-4 w-full">
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
                                                    @endforeach</x-form.select>
                                            </div>
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
                                        </div>
                                        @if(count($members) > 1)
                                            <button type="button" wire:click="removeMember({{ $index }})" class="absolute top-1.5 right-1.5 p-2 rounded-md hover:bg-red-100 group">
                                                <x-svg.trash class="group-hover:text-red-500"/>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" wire:click="addMember" class="button-classic w-fit md:mx-1">
                            <x-svg.add2/>
                            Ajouter un autre membre
                        </button>
                    </div>

                    {{-- Boutons de navigation --}}
                    <x-slot:footer>
                        <div class="flex items-center justify-between flex-wrap gap-2 lg:gap-3">
                            <button type="button" wire:click="previousStep" class="button-primary">
                                <x-svg.arrows.left class="text-slate-900"/>
                                Retour
                            </button>
                            <div class="flex items-center justify-end flex-wrap gap-2 lg:gap-3">
                                <button type="button" wire:click="skipInvitations" class="button-primary">Ignorer</button>
                                <button type="button" wire:click="nextStep" class="button-tertiary">Continuer
                                    <x-svg.arrows.right class="text-white"/>
                                </button>
                            </div>
                        </div>
                    </x-slot:footer>
                </x-onboarding.step-layout>
                @break

            @case(3)
                <x-onboarding.step-layout
                    step="3" title="Récapitulatif"
                    description="Vérifiez les informations avant de finaliser la création de votre famille."
                >
                    <div class="flex-grow space-y-6 overflow-y-auto -mr-4 pr-4">
                        <div class="pt-4 pb-2 px-5 rounded-lg bg-gray-50 border border-slate-200">
                            <h3 role="heading" aria-level="3" class="text-lg-medium text-slate-800 mb-4">
                                Informations générales
                            </h3>
                            <div class="divide-y divide-gray-200">
                                <div class="flex items-center justify-between flex-wrap gap-2 py-3">
                                    <span class="text-sm text-gray-500">Nom de la famille</span>
                                    <span class="capitalize text-sm-medium text-slate-800 truncate max-w-60 lg:max-w-80">{{ $form->familyName }}</span>
                                </div>
                                <div class="flex items-center justify-between py-3">
                                    <span class="text-sm text-gray-500">Créateur</span>
                                    <span class="capitalize text-sm-medium text-slate-800">{{ auth()->user()->name }}</span>
                                </div>
                                <div class="flex items-center justify-between flex-wrap gap-2 py-3">
                                    <span class="text-sm text-gray-500">Votre rôle</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs-medium truncate max-w-60 lg:max-w-80 {{ App\Enums\FamilyPermissionEnum::Admin->cssClasses() }}">
                                        {{ App\Enums\FamilyPermissionEnum::Admin->label() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if(count(array_filter($members, fn($m) => !empty($m['email']) && $m['valid'])) > 0)
                            <div class="pt-4 pb-2 px-5 rounded-lg bg-gray-50 border border-slate-200">
                                <h3 role="heading" aria-level="3" class="text-lg-medium text-slate-800 mb-2">
                                    Membres invités
                                </h3>
                                <div class="space-y-1">
                                    @foreach($members as $member)
                                        @if(!empty($member['email']) && $member['valid'])
                                            <div class="flex justify-between items-center flex-wrap gap-2 py-2">
                                                <div>
                                                    <p class="text-sm-medium text-slate-800 truncate max-w-60 md:max-w-80">
                                                        {{ $member['email'] }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">
                                                        {{ App\Enums\FamilyRelationEnum::tryFrom($member['relation'])?->label() }}
                                                    </p>
                                                </div>
                                                <div>@php $permission = App\Enums\FamilyPermissionEnum::tryFrom($member['permission']); @endphp
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs-medium {{ $permission?->cssClasses() }}">
                                                        {{ $permission?->label() }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach</div>
                            </div>
                        @endif
                        <div class="bg-blue-50 rounded-xl py-3 px-4 flex gap-3 border border-blue-100">
                            <x-svg.info class="relative top-0.5 min-w-3 text-blue-500"/>
                            <p class="text-sm text-blue-700">
                                En cliquant sur "Créer ma famille", vous finaliserez sa création et les invitations seront envoyées.
                            </p>
                        </div>
                    </div>

                    {{-- Boutons de navigation --}}
                    <x-slot:footer>
                        <div class="flex justify-between">
                            <button type="button" wire:click="previousStep" class="button-primary">
                                <x-svg.arrows.left class="text-slate-900"/>
                                Retour
                            </button>
                            <button type="button" wire:click="submitForm" class="button-tertiary" wire:loading.attr="disabled">
                                <x-svg.add2 class="text-white"/>
                                Créer ma famille
                            </button>
                        </div>
                    </x-slot:footer>
                </x-onboarding.step-layout>
                @break

            @case(4)
                <div
                     x-data="confetti"
                     x-init="init()"
                     class="flex-center flex-col gap-6 pb-2"
                >
                    <x-svg.success class="h-12 w-12 text-green-500"/>
                    <h2 role="heading" aria-level="2" class="text-3xl font-semibold text-slate-800 text-center">
                        Félicitations !
                    </h2>
                    <p class="text-gray-600 max-w-md text-center">
                        Votre famille <span class="font-medium text-slate-800">{{ $form->familyName }}</span> a été créée avec succès.
                        @if($this->hasInvitedMembers())
                            <span class="block mt-2 px-12">Les invitations ont été envoyées aux membres que vous avez ajoutés.</span>
                        @endif
                    </p>
                    <button type="button" wire:click="finishOnboarding" class="button-success">
                        Commencer à utiliser l'application
                        <x-svg.validate class="text-white"/>
                    </button>
                </div>
                @break
        @endswitch
    @endif
</div>

@assets
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
@endassets

@script
<script>
    Alpine.data('confetti', () => {
        return {
            init() {
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
        };
    });
</script>
@endscript
