<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 p-6 rounded-xl bg-white border border-slate-200">
        {{-- Section Profil --}}
        <section>
            <header class="mb-6">
                <h2 class="text-xl-medium text-gray-800 dark:text-gray-100">
                    {{ __('Informations du profil') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __("Personnalisez votre profil et mettez à jour vos informations de connection.") }}
                </p>
            </header>

            <form wire:submit="updateProfileInformation" class="space-y-6">
                @csrf

                <div class="flex flex-col md:flex-row gap-6 items-start">
                    {{-- Avatar display --}}
                    <div class="relative lg:pl-4">
                        <div class="flex-center h-20 w-20 rounded-full overflow-hidden bg-zinc-100 border border-slate-200">
                            @if($form->avatarUrl)
                                <img src="{{ $form->avatarUrl }}" alt="Avatar" class="h-full w-full object-cover" loading="lazy">
                            @else
                                <span class="text-zinc-400 display-sm-regular">{{ substr($form->name, 0, 1) }}</span>
                            @endif
                        </div>

                        {{-- Preview overlay --}}
                        @if($form->avatar && !$errors->has('form.avatar'))
                            <div wire:loading.remove wire:target="form.avatar" class="lg:pl-4 absolute inset-0">
                                <div class="h-20 w-20 rounded-full overflow-hidden ring-1 ring-indigo-200">
                                    <img src="{{ $form->avatar->temporaryUrl() }}" alt="Aperçu" class="h-full w-full object-cover" loading="lazy">
                                </div>
                                <div class="absolute -bottom-0.5 -right-0.5 bg-white rounded-full">
                                    <x-svg.success class="w-5 h-5 text-teal-500"/>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Avatar upload --}}
                    <div class="flex flex-col gap-3 w-full">
                        @error('form.avatar')
                            <p class="py-3 px-4 max-md:mb-4 rounded-lg border-red-100 bg-red-100 text-sm-medium text-red-500">
                                {{ $message }}
                            </p>
                        @else
                            <p class="px-2 text-sm text-gray-500 dark:text-gray-400">
                                {{ __('Sélectionnez une image pour votre photo de profil.') }}
                            </p>
                        @enderror

                            <div class="flex flex-wrap gap-4">

                                <div x-data="{
                                    fileError: null,
                                    maxSizeBytes: 5 * 1024 * 1024,

                                    validateFile(event) {
                                        const file = event.target.files[0];
                                        if (!file) return true;

                                        if (file.size > this.maxSizeBytes) {
                                            this.fileError = `Le fichier est trop volumineux (${(file.size / (1024 * 1024)).toFixed(2)} Mo). Maximum 5 Mo.`;
                                            this.resetFile(event.target);
                                            return false;
                                        }

                                        this.fileError = null;
                                        return true;
                                    },

                                    resetFile(input) {
                                        input.value = '';
                                        $wire.set('form.avatar', null);
                                    }
                                }">
                                    <label for="avatar" class="button-primary cursor-pointer">
                                        <x-svg.file.img class="mr-1" />
                                        {{ __('Choisir une photo de profil') }}
                                    </label>
                                    <input
                                        id="avatar"
                                        type="file"
                                        wire:model="form.avatar"
                                        @change="validateFile($event)"
                                        class="sr-only"
                                        accept="image/jpg,image/jpeg,image/png"
                                    />

                                    <div x-show="fileError" x-cloak class="mt-2 px-3 py-2 bg-red-100 text-red-800 text-xs rounded-md" x-text="fileError"></div>
                                </div>

                                @if($form->avatarUrl)
                                    <button type="button" wire:click="deleteAvatar" class="button-classic group">
                                        <x-svg.trash class="group-hover:text-gray-900" />
                                        {{ __('Supprimer') }}
                                    </button>
                                @endif
                            </div>

                            <p class="pl-2 text-xs text-gray-500">
                                {{ __('JPG, JPEG, PNG · Max: 5MB') }}
                            </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <x-form.field
                        label="Nom d'utilisateur"
                        name="name"
                        model="form.name"
                        placeholder="Nom"
                        class="capitalize"
                    />

                    <x-form.field
                        label="Adresse email"
                        name="email"
                        model="form.email"
                        type="email"
                        placeholder="votre-email@gmail.com"
                        class="lowercase"
                    />
                </div>

                {{-- Upload progress indicator --}}
                <x-loader.animation
                    target="form.avatar"
                    title="Importation en cours..."
                    description="Préparation de l'image pour votre profil"
                />

                {{-- Email verification alert --}}
                @if (!$hasVerifiedEmail)
                    <div class="px-4 py-3 bg-indigo-50 dark:bg-indigo-900/10 rounded-lg lg:-mb-6">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <p class="flex items-center gap-3 text-sm-medium text-indigo-700">
                                <x-svg.info class="text-indigo-700" />
                                {{ __('Cette nouvelle adresse email n\'a pas encore été vérifiée.') }}
                            </p>

                            <button type="button" wire:click="sendVerification" class="button-tertiary">
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
            <header>
                <h2 class="text-xl-medium text-gray-800 dark:text-gray-100">
                    {{ __('Sécurité') }}
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Mettez à jour votre mot de passe pour sécuriser votre compte.') }}
                </p>
            </header>

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
                        name="passwordForm.current_password"
                        model="passwordForm.current_password"
                        required
                    />

                    <x-form.field-password
                        label="Nouveau mot de passe"
                        name="passwordForm.password"
                        model="passwordForm.password"
                        required
                    />

                    <x-form.field-password
                        label="Confirmer le nouveau mot de passe"
                        name="passwordForm.password_confirmation"
                        model="passwordForm.password_confirmation"
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
