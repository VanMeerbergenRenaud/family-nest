<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 p-6 rounded-xl bg-white border border-slate-200">

        {{-- Section Profil --}}
        <section>
            <div class="mb-6">
                <h2 class="text-xl font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Informations du profil') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __("Personnalisez votre profil et mettez à jour vos informations de connection.") }}
                </p>
            </div>

            <form wire:submit="updateProfileInformation" class="space-y-6">
                @csrf

                <div class="flex flex-col md:flex-row gap-6 items-start">
                    <div class="relative lg:pl-4">

                        <!-- Avatar actuel ou avatar par défaut -->
                        <div class="flex-center h-20 w-20 rounded-full overflow-hidden bg-zinc-100 border border-slate-200">
                            @if($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full object-cover">
                            @else
                                <span class="text-zinc-400 text-3xl">{{ substr($name, 0, 1) }}</span>
                            @endif
                        </div>

                        <!-- Aperçu de l'upload -->
                        @if($avatar && !$avatarError)
                            <div wire:loading.remove wire:target="avatar" class="lg:pl-4 absolute inset-0">
                                <div class="h-20 w-20 rounded-full overflow-hidden ring-1 ring-indigo-200">
                                    <img src="{{ $avatar->temporaryUrl() }}" alt="Aperçu" class="h-full w-full object-cover">
                                </div>
                                <!-- Indicateur : succès / erreur -->
                                <div class="absolute -bottom-0.5 -right-0.5 bg-white rounded-full">
                                    @if($errors->has('avatar'))
                                        <x-svg.error class="w-5 h-5 text-red-500"/>
                                    @else
                                        <x-svg.success class="w-5 h-5 text-teal-500"/>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Upload de l'avatar --}}
                    <div class="flex flex-col gap-3 w-full">
                        @if(!$errors->has('avatar'))
                            <p class="px-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Sélectionnez une image pour votre photo de profil.') }}
                            </p>
                        @else
                            @error('avatar')
                               <p class="py-3 px-4 max-md:mb-4 rounded-lg border-red-100 bg-red-100 text-sm-medium text-red-500">
                                    {{ $message }}
                               </p>
                            @enderror
                        @endif

                        <div class="flex flex-wrap gap-4">
                            <label for="avatar" class="button-primary cursor-pointer">
                                <x-svg.file.img class="mr-1" />
                                {{ __('Choisir une photo de profil') }}
                                <input id="avatar" type="file" wire:model="avatar" class="hidden" accept="image/jpg,image/jpeg,image/png"/>
                            </label>

                            @if($avatarUrl)
                                <button type="button" wire:click="deleteAvatar" class="button-classic group">
                                    <x-svg.trash class="group-hover:text-gray-900" />
                                    {{ __('Supprimer') }}
                                </button>
                            @endif
                        </div>

                        <p class="pl-2 text-xs text-gray-500">
                            {{ __('JPG, JPEG, PNG · Max: 1MB') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <x-form.field
                        label="Nom"
                        name="name"
                        model="name"
                        placeholder="Renseigner votre nom"
                    />

                    <x-form.field
                        label="Adresse email"
                        name="email"
                        model="email"
                        type="email"
                        placeholder="Renseignez votre adresse email"
                    />
                </div>

                <!-- Progressive upload indicator at bottom -->
                <x-alert-loading
                    target="avatar"
                    title="Téléchargement en cours..."
                    description="Préparation de l'image pour votre profil"
                />

                {{-- Email verification --}}
                @if (!$hasVerifiedEmail)
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-lg lg:-mb-6">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <p class="flex items-center gap-3 text-sm-medium text-indigo-700">
                                <x-svg.info class="text-indigo-700" />
                                {{ __('Cette nouvelle adresse email n\'a pas encore été vérifiée.') }}
                            </p>

                            <button
                                type="button"
                                wire:click="sendVerification"
                                class="button-tertiary"
                            >
                                {{ __('Envoyer le lien de vérification') }}
                            </button>
                        </div>
                    </div>
                @endif

                <div class="flex justify-end lg:mt-16">
                    <div class="flex gap-3">
                        <button type="button" wire:click="cancelProfileEdit" class="button-secondary">
                            {{ __('Annuler') }}
                        </button>

                        <button type="submit" class="button-primary">
                            {{ __('Sauvegarder') }}
                        </button>
                    </div>
                </div>
            </form>
        </section>

        {{-- Section Mot de passe --}}
        <section class="space-y-4 border-t lg:border-l lg:border-t-0 border-gray-100 dark:border-gray-800 lg:pl-8 pt-8 lg:pt-0">
            <div>
                <h2 class="text-xl font-medium text-gray-800 dark:text-gray-100">
                    {{ __('Sécurité') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Mettez à jour votre mot de passe pour sécuriser votre compte.') }}
                </p>
            </div>

            <form wire:submit="updatePassword" class="space-y-6">
                @csrf

                <div class="p-4 border border-indigo-100 dark:border-indigo-900/20 bg-indigo-50/50 dark:bg-indigo-900/5 rounded-lg">
                    <div class="flex gap-3 text-sm text-indigo-700 dark:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                  d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                  clip-rule="evenodd"/>
                        </svg>
                        {{ __('Le mot de passe doit contenir au moins 8 caractères.') }}
                    </div>
                </div>

                <div class="flex flex-col gap-6">
                    <x-form.field-password
                        label="Mot de passe actuel"
                        name="update_password_current_password"
                        model="current_password"
                        required
                    />

                    <x-form.field-password
                        label="Nouveau mot de passe"
                        name="update_password_password"
                        model="password"
                        required
                    />

                    <x-form.field-password
                        label="Confirmer le nouveau mot de passe"
                        name="update_password_password_confirmation"
                        model="password_confirmation"
                        required
                    />
                </div>

                <div class="flex justify-end">
                    <div class="flex gap-3">
                        <button type="button" wire:click="cancelPasswordEdit" class="button-secondary">
                            {{ __('Annuler') }}
                        </button>

                        <button type="submit" class="button-primary">
                            {{ __('Sauvegarder') }}
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
