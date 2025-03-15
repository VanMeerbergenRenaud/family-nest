<div class="grid grid-cols-[40vw_auto] mx-auto lg:gap-8">
    <img
        src="{{ $file_path ?? asset('img/avatar_placeholder.png') }}"
        alt="facture"
        class="rounded-xl w-full object-contain"
    >
    <x-invoices.create.summary :form="$invoice" />
</div>
