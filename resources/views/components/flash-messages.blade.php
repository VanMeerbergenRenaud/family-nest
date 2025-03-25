{{-- Messages d'erreur --}}
@if (session('error'))
    <div class="flex items-center p-4 mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800">
        <x-svg.error class="w-5 h-5 text-red-500 dark:text-red-400 mr-3 flex-shrink-0"/>
        <div class="text-sm-regular text-red-700 dark:text-red-300">
            <p>{{ session('error') }}</p>
        </div>
    </div>
@endif

{{-- Messages de succès --}}
@if (session('success'))
    <div class="flex items-center p-4 mb-6 rounded-xl bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800">
        <x-svg.success class="w-5 h-5 text-green-500 dark:text-green-400 mr-3 flex-shrink-0"/>
        <div class="text-sm-regular text-green-700 dark:text-green-300">
            <p>{{ session('success') }}</p>
        </div>
    </div>
@endif

{{-- Messages de statut --}}
@if (session('status'))
    <div class="flex items-center p-4 mb-6 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800">
        <x-svg.info class="w-5 h-5 text-blue-500 dark:text-blue-400 mr-3 flex-shrink-0"/>
        <div class="text-sm-regular text-blue-700 dark:text-blue-300">
            <p>{{ session('status') }}</p>
        </div>
    </div>
@endif

{{-- Messages d'avertissement --}}
@if (session('warning'))
    <div class="flex items-center p-4 mb-6 rounded-xl bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800">
        <x-svg.warning class="w-5 h-5 text-amber-500 dark:text-amber-400 mr-3 flex-shrink-0"/>
        <div class="text-sm-regular text-amber-700 dark:text-amber-300">
            <p>{{ session('warning') }}</p>
        </div>
    </div>
@endif
