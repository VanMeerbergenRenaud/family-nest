@props([
    'label' => null,
    'model' => '',
    'name' => '',
    'asterix' => false,
    'placeholder' => '0,00',
    'initialValue' => null,
    'defaultCurrency' => 'EUR'
])

@php
    $id = $name ?? uniqid('currency-field-');
    $currencies = [];
    $symbols = [];

    foreach (App\Enums\InvoiceCurrencyEnum::cases() as $currency) {
        $currencies[$currency->value] = [
            'symbol' => $currency->symbol(),
            'name' => $currency->name(),
            'flag' => $currency->flag()
        ];
        $symbols[$currency->value] = $currency->symbol();
    }

    $symbolsJson = json_encode($symbols);
@endphp

<div class="m-0 p-0 max-w-[45rem]">
    @if($label)
        <label for="{{ $id }}" class="relative mb-1.5 pl-2 block text-sm-medium text-gray-800 dark:text-gray-200">
            {{ ucfirst($label) }}@if($asterix)<span class="absolute -top-0.5 ml-0.5 text-rose-500">*</span>@endif
        </label>
    @endif

    <div x-data="currencyField('{{ $id }}', {{ $symbolsJson }}, '{{ $defaultCurrency }}', {{ is_numeric($initialValue) ? $initialValue : 'null' }}, '{{ $model }}')">
        <div class="relative flex items-center justify-between rounded-md bg-white border border-slate-200 dark:border-gray-600 @error($name) input-invalid @enderror">
            <span class="pl-3 rounded-l-md text-gray-500 dark:text-gray-300" x-text="symbols[currency]"></span>

            <input
                type="text"
                id="{{ $id }}"
                name="{{ $name }}"
                x-model="amount"
                x-mask:dynamic="$money($input, ',', ' ', 2)"
                @blur="updateLivewire()"
                class="rounded-0 p-2.5 border-0 flex-grow w-[calc(100%-1.5rem)] bg-white text-sm-regular focus:outline-0 text-gray-700 dark:bg-gray-700 dark:text-white"
                placeholder="{{ $placeholder }}"
                {{ $attributes }}
            />

            <x-menu>
                <x-menu.button class="flex-center rounded-r-md pl-3 pr-2 py-2.5 gap-1.5 bg-gray-50 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500 border-l border-slate-200 dark:border-gray-600 transition-colors">
                    <span x-text="currency" class="text-sm-medium"></span>
                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </x-menu.button>

                <x-menu.items class="mt-2 w-72 max-h-80 overflow-y-scroll rounded-md shadow-lg bg-white dark:bg-gray-800 z-20">
                    @foreach($currencies as $code => $currencyInfo)
                        <x-menu.item @click="setCurrency('{{ $code }}')">
                            <span class="relative w-full flex items-center gap-3">
                                <span class="min-w-6 text-sm-regular">{{ $currencyInfo['flag'] }}</span>
                                <span class="min-w-8 text-sm-medium text-gray-900 dark:text-white">{{ $code }}</span>
                                <span class="min-w-10 text-center text-sm-medium text-gray-500 dark:text-gray-400">{{ $currencyInfo['symbol'] }}</span>
                                <span class="pr-2 text-xs-regular text-gray-500 dark:text-gray-400">{{ $currencyInfo['name'] }}</span>
                                <svg x-show="currency === '{{ $code }}'" class="h-4 w-4 absolute right-0.75 top-0.85"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </x-menu.item>
                    @endforeach
                </x-menu.items>
            </x-menu>
        </div>

        @error($model)
        <ul class="my-2 flex flex-col gap-2 font-medium text-red-500 dark:text-red-400">
            @foreach ($errors->get($model) as $error)
                <li class="pl-2 pr-1 text-sm-medium text-red-500 dark:text-red-400">{{ $error }}</li>
            @endforeach
        </ul>
        @enderror
    </div>
</div>

@script
<script>
    Alpine.data('currencyField', (id, symbols, defaultCurrency, initialValue, model) => {
        return {
            amount: '',
            numericValue: initialValue,
            currency: '',
            symbols: symbols,

            init() {
                // Initialiser la devise
                this.currency = this.$wire.get('form.currency') || defaultCurrency;

                // Si une valeur initiale est fournie, l'utiliser pour formater l'affichage
                if (this.numericValue !== null) {
                    this.formatDisplayFromNumeric();
                }

                // Observer les changements de valeur venant de Livewire
                this.$wire.$watch(model, (val) => {
                    this.numericValue = val;
                    this.formatDisplayFromNumeric();
                });
            },

            // Formater la valeur numérique pour l'affichage
            formatDisplayFromNumeric() {
                if (this.numericValue === null || this.numericValue === undefined || this.numericValue === '') {
                    this.amount = '';
                    return;
                }

                // Formater avec le format français (virgule comme séparateur décimal)
                let num = parseFloat(this.numericValue).toFixed(2);
                let [int, dec] = num.split('.');

                // Ajouter les séparateurs de milliers
                int = int.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

                // Assembler avec virgule
                this.amount = int + ',' + dec;
            },

            // Mettre à jour Livewire lors de la sortie du champ
            updateLivewire() {
                if (!this.amount) {
                    this.numericValue = null;
                    this.$wire.set(model, null);
                    return;
                }

                // Nettoyer et convertir en nombre
                let parsed = this.amount
                    .replace(/\s/g, '')     // Supprimer les espaces
                    .replace(/,/g, '.')     // Remplacer virgules par points
                    .replace(/[^\d.]/g, '') // Garder seulement chiffres et points
                    .replace(/(\..*)\./g, '$1'); // Garder un seul point décimal

                let num = parseFloat(parsed);
                if (!isNaN(num)) {
                    this.numericValue = num;
                    this.$wire.set(model, num);
                }
            },

            // Définir la devise
            setCurrency(code) {
                this.currency = code;
                this.$wire.set('form.currency', code);
            }
        }
    });
</script>
@endscript
