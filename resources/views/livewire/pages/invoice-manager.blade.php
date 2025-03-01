<div>
    @if($invoices->isEmpty())
        <p>
            {{ __('Aucune facture pour le moment.') }}
        </p>
    @endif

    <div class="flex justify-end items-center max-w-[97vw]">
        <a href="{{ route('invoices.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out">
            <x-svg.add2 class="w-4 h-4 mr-2" />
            {{ __('Ajouter une facture') }}
        </a>
    </div>

    <div class="mt-4 overflow-x-auto mx-auto max-w-[95vw] rounded-lg border border-b-gray-300">
        <table class="min-w-full divide-y divide-gray-200 overflow-hidden bg-white">
            <thead class="bg-gray-100">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Nom
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Fournisseur
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Montant
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Priorité
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tags
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" wire:loading.class.delay="opacity-50">
            @foreach ($invoices as $invoice)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $invoice->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $invoice->issuer }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $invoice->type }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $invoice->amount }}€
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="
                            @if($invoice->status === 'paid') text-green-700 bg-green-100 @endif
                            @if($invoice->status === 'unpaid') text-yellow-700 bg-yellow-100 @endif
                            @if($invoice->status === 'late') text-red-700 bg-red-100 @endif
                            @if($invoice->status === 'partially_paid') text-blue-700 bg-blue-100 @endif
                            px-2 py-1 rounded-full text-xs font-semibold
                        ">
                            {{
                                 __([
                                    'unpaid' => 'Non payée',
                                    'paid' => 'Payée',
                                    'late' => 'En retard',
                                    'partially_paid' => 'Partiellement payée'
                                 ][$invoice->status])
                            }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span>
                             {{
                                __([
                                    'high' => 'Élevée',
                                    'medium' => 'Moyenne',
                                    'low' => 'Basse',
                                 ][$invoice->priority])
                             }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex space-x-1">
                            @foreach($invoice->tags as $tag)
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <x-menu>
                            <x-menu.button
                                class="flex items-center justify-center w-8 h-8 rounded-full hover:bg-gray-100 transition-colors duration-200">
                                <x-svg.dots class="w-5 h-5 text-gray-500"/>
                            </x-menu.button>

                            <x-menu.items
                                class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <x-menu.item wire:click="showFile({{ $invoice->id }})"
                                             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                                    <x-svg.show class="w-4 h-4 mr-2 inline-block"/>
                                    {{ __('Voir la facture') }}
                                </x-menu.item>

                                <x-divider />

                                <x-menu.item wire:click="showEditForm({{ $invoice->id }})"
                                             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                                    <x-svg.edit class="w-4 h-4 mr-2 inline-block"/>
                                    {{ __('Modifier') }}
                                </x-menu.item>

                                <x-divider />

                                <x-menu.item wire:click="showDeleteForm({{ $invoice->id }})"
                                             class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-100 hover:text-red-600 transition-colors duration-200">
                                    <x-svg.trash class="w-4 h-4 inline-block text-red-500"/>
                                    {{ __('Supprimer') }}
                                </x-menu.item>

                                <x-divider />

                                <x-menu.item wire:click="downloadInvoice({{ $invoice->id }})"
                                             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200">
                                    <x-svg.download class="w-4 h-4 mr-2 inline-block"/>
                                    {{ __('Télécharger') }}
                                </x-menu.item>
                            </x-menu.items>
                        </x-menu>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modale qui affiche la facture -->
    <x-modal wire:model="showFileModal">
        <x-modal.panel>
            <div class="sticky top-0 p-5 px-8 max-w-full text-xl font-bold border-b border-[#dde2e9] bg-white z-20">
                {{ __('Aperçu du fichier') }}
            </div>
            <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
            <x-modal.footer>
                <x-modal.close>
                    <button type="button">
                        {{ __('Fermer') }}
                    </button>
                </x-modal.close>
            </x-modal.footer>
        </x-modal.panel>
    </x-modal>

    <!-- Modale de modification d'une facture -->
    <x-modal wire:model="showEditFormModal">
        <x-modal.panel>
            <div class="sticky top-0 p-5 px-8 max-w-full text-xl font-bold border-b border-[#dde2e9] bg-white z-10">
                {{ __('Modifier votre facture juste ici 👇') }}
            </div>

            <form wire:submit.prevent="updateInvoice">
                @csrf

                <div class="flex flex-col gap-4 p-8">
                    <x-form.field label="Nom de la facture" name="name" model="name"/>
                    <x-form.field label="Fournisseur / Émetteur" name="issuer" model="issuer"/>
                    <x-form.field label="Type de facture" name="type" model="type"/>
                    <x-form.field label="Catégorie de la facture" name="category" model="category"/>
                    <x-form.field label="Site internet du fournisseur" name="website" type="url" model="website"/>

                    <div>
                        <label for="amount">Montant (€)</label>
                        <input type="text" x-mask:dynamic="$money($input, '.', ' ')" wire:model="amount"
                               id="amount">
                        @error('amount') <span>{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label>Montant variable</label>
                        <div>
                            <input type="radio" wire:model="is_variable" id="is_variable_yes" value="1">
                            <label for="is_variable_yes">Oui</label>
                        </div>
                        <div>
                            <input type="radio" wire:model="is_variable" id="is_variable_no" value="0">
                            <label for="is_variable_no">Non</label>
                        </div>
                    </div>

                    <div>
                        <label>Associé à un membre de la famille</label>
                        <div>
                            <input type="radio" wire:model="is_family_related" id="is_family_related_yes" value="1">
                            <label for="is_family_related_yes">Oui</label>
                        </div>
                        <div>
                            <input type="radio" wire:model="is_family_related" id="is_family_related_no" value="0">
                            <label for="is_family_related_no">Non</label>
                        </div>
                    </div>

                    <x-form.field label="Date d'émission"
                                  name="issued_date"
                                  type="date"
                                  model="issued_date"
                                  min="2020-01-01T00:00"
                                  placeholder="{{ now()->format('d-m-Y') }}"
                    />

                    <x-form.field label="Rappels de paiement" name="payment_reminder" model="payment_reminder"/>
                    <x-form.field label="Fréquence de paiement" name="payment_frequency" model="payment_frequency"/>

                    <div>
                        <label for="status">Statut de la facture</label>
                        <select wire:model="status" id="status">
                            <option value="unpaid">Non-payée</option>
                            <option value="paid">Payée</option>
                            <option value="late">En retard</option>
                            <option value="partially_paid">Partiellement payée</option>
                        </select>
                        @error('status') <span>{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="payment_method">Méthode de paiement</label>
                        <select wire:model="payment_method" id="payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Bancontact</option>
                            <option value="mastercard">Visa/Mastercard</option>
                        </select>
                        @error('payment_method') <span>{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="priority">Priorité</label>
                        <select wire:model="priority" id="priority">
                            <option value="high">Élevée</option>
                            <option value="medium">Moyenne</option>
                            <option value="low">Basse</option>
                        </select>
                        @error('priority') <span>{{ $message }}</span> @enderror
                    </div>

                    <x-form.field-textarea
                        label="Notes"
                        name="notes"
                        model="notes"
                        placeholder="Ajoutez des notes ici"
                    />

                    <div class="mb-4 flex flex-col gap-2">
                        <label for="tagInput" class="block text-sm font-medium text-gray-700">Tags</label>
                        <div class="mt-1 flex rounded-md">
                            <input type="text" wire:model="tagInput" id="tagInput"
                                   class="border py-2 px-4 border-b-gray-200 flex-1 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                   placeholder="Ajouter un tag">
                            <button type="button" wire:click="addTag"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Ajouter
                            </button>
                        </div>

                        @error('tags') <span class="text-sm text-red-600">{{ $message }}</span> @enderror

                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($tags as $index => $tag)
                                <span
                                    class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $index }})"
                                            class="ml-2 inline-flex items-center justify-center h-4 w-4 rounded-full bg-indigo-200 text-indigo-600 hover:bg-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <span class="sr-only">Supprimer tag</span>
                                        <x-svg.cross class="h-3 w-3"/>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <x-modal.footer>
                    <x-modal.close>
                        <button type="button" class="cancel">
                            {{ __('Annuler') }}
                        </button>
                    </x-modal.close>

                    <button type="submit" class="save">
                        {{ __('Mettre à jour') }}
                    </button>
                </x-modal.footer>
            </form>
        </x-modal.panel>
    </x-modal>

    <!-- Modale de suppression d'une facture -->
    <x-modal wire:model="showDeleteFormModal">
        <x-modal.panel>
            <form wire:submit.prevent="deleteInvoice">
                @csrf

                <div x-data="{ confirmation: '' }">
                    <div>
                        <div class="flex gap-x-6 p-8">
                            <x-svg.advertising/>

                            <div>
                                <h3 role="heading" aria-level="3" class="mb-4 font-semibold text-xl leading-6">
                                    {{ __('Supprimer la facture') }}
                                </h3>
                                <p class="mt-2 text-sm leading-5 text-gray-500">
                                    {{ __('Êtes-vous sûre de vouloir supprimer la facture') }}
                                    <strong class="font-semibold"> {{ $invoice->name }}</strong>&nbsp;
                                    {{ __('? Toutes les données seront supprimées. Cette action est irréversible.') }}
                                </p>
                                <div class="mt-6 mb-2 flex flex-col gap-3">
                                    <label for="confirmation" class="text-base font-medium">
                                        {{ __('Veuillez tapper "CONFIRMER" pour confirmer la suppression.') }}
                                    </label>
                                    <input x-model="confirmation" placeholder="CONFIRMER" type="text" id="confirmation"
                                           class="p-2 px-4 text-sm border border-gray-300 rounded-md w-[87.5%]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-modal.footer>
                        <x-modal.close>
                            <button type="button">
                                {{ __('Annuler') }}
                            </button>
                        </x-modal.close>

                        <x-modal.close>
                            <button
                                type="submit"
                                :disabled="confirmation !== 'CONFIRMER'"
                            >
                                {{ __('Supprimer') }}
                            </button>
                        </x-modal.close>
                    </x-modal.footer>
                </div>
            </form>
        </x-modal.panel>
    </x-modal>

    @if($addedWithSuccess)
        <x-flash-message
            icon="add"
            title="Facture ajoutée avec succès !"
            message="Vous avez ajouté une nouvelle facture."
            method="$set('addedWithSuccess', false)"
        />
    @endif

    @if($editWithSuccess)
        <x-flash-message
            icon="edit"
            title="Facture modifiée avec succès !"
            message="Vous avez modifié une facture."
            method="$set('editWithSuccess', false)"
        />
    @endif

    @if($deleteWithSuccess)
        <x-flash-message
            icon="delete"
            title="Facture supprimée avec succès !"
            message="Vous avez supprimé une facture."
            method="$set('deleteWithSuccess', false)"
        />
    @endif
</div>
