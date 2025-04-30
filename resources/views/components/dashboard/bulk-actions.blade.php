@props([
    'invoices',
])

<div x-cloak
     x-transition
     x-show="$wire.selectedInvoiceIds.length > 0"
     class="fixed bottom-4 left-6 right-6 lg:left-24 mx-auto w-auto lg:w-fit z-70 rounded-md lg:rounded-xl bg-gray-100 border border-gray-200"
>
    <div class="flex max-lg:flex-wrap items-center justify-between gap-y-4 p-2.5 lg:pl-5 lg:pr-2.5">
        <!-- Selected count indicator -->
        <div class="flex items-center gap-1">
            <span x-text="$wire.selectedInvoiceIds.length" class="text-gray-900 text-sm mr-0.5"></span>
            <span class="text-gray-500 text-sm">sur</span>
            <span class="text-gray-600 text-sm">
                    {{ $invoices->total() }} {{ Str::plural('facture', $invoices->total()) }}
                </span>
        </div>

        <div class="max-lg:hidden h-5 w-px bg-gray-300 mx-3"></div>

        <!-- Action buttons -->
        <div class="lg:ml-1 flex flex-wrap items-center gap-2">
            <!-- Archive button -->
            <form wire:submit="archiveSelected">
                <button type="submit" class="button-primary group hover:text-red-500">
                    <x-svg.archive class="w-4 h-4 group-hover:text-red-500"/>
                    Archiver
                </button>
            </form>

            <!-- Mark as 'paid, unpaid, late,..' select -->
            <form wire:submit="markAsPaymentStatusSelected">
                <label for="selectedPaymentStatus" class="sr-only">Statut de paiement</label>
                <div x-data="{ selectedOption: '' }" class="flex flex-wrap items-center gap-2">
                    <div class="relative inline-block">
                        <select
                            id="selectedPaymentStatus"
                            wire:model="selectedPaymentStatus"
                            x-model="selectedOption"
                            class="button-primary h-[2.625rem] pr-9 pl-3 appearance-none cursor-pointer"
                        >
                            <option value="" selected>Changer le statut</option>
                            @foreach(App\Enums\PaymentStatusEnum::cases() as $status)
                                <option value="{{ $status->value }}">
                                    {{ $status->emoji() }}&nbsp;&nbsp;{{ $status->label() }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Flèche personnalisée -->
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <x-svg.arrows.right class="rotate-90" />
                        </div>
                    </div>

                    <button type="submit" class="button-secondary bg-slate-700 hover:bg-slate-800" x-show="selectedOption">
                        <x-svg.validate class="text-white"/>
                        Appliquer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
