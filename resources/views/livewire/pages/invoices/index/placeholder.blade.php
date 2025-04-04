<div class="animate-pulse">
    <!-- Catégories Skeleton -->
    <section class="mb-8">
        <div class="h-6 w-36 bg-gray-200 dark:bg-gray-700 rounded mb-3"></div>

        <div class="flex overflow-x-scroll gap-4 scrollbar-hidden">
            @foreach (range(1, 6) as $i)
                <div class="inline-block p-5 pb-4 min-w-52 rounded-xl bg-gray-200 dark:bg-gray-800">
                    <div class="flex flex-col text-left">
                        <div class="mb-3.5 p-3 rounded-lg w-12 h-12 bg-gray-300 dark:bg-gray-700"></div>
                        <div class="h-5 w-20 bg-gray-300 dark:bg-gray-700 rounded"></div>
                        <div class="h-4 w-32 bg-gray-300 dark:bg-gray-700 rounded mt-1"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Factures récentes Skeleton -->
    <section class="mb-10 animate-pulse">
        <div class="h-6 w-44 bg-gray-200 dark:bg-gray-700 rounded mb-3"></div>

        <ul class="flex overflow-x-scroll gap-4 scrollbar-hidden">
            @foreach (range(1, 6) as $i)
                <li class="pl-4 py-4 pr-3 min-w-fit h-fit rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between gap-4">
                        <div class="bg-gray-200 dark:bg-gray-700 p-3 w-12 h-12 rounded-lg"></div>
                        <div>
                            <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-2"></div>
                            <div class="h-4 w-28 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                        <div class="h-5 w-5 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <!-- Tableau des factures Skeleton -->
    <section class="w-full overflow-hidden bg-white dark:bg-gray-800 rounded-2xl border border-slate-200 animate-pulse">
        <!-- En-tête -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 border-b border-gray-200 dark:border-gray-700">
            <div class="h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded mb-3 sm:mb-0"></div>
            <div class="flex flex-wrap gap-2">
                <div class="h-10 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-10 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                <div class="h-10 w-36 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>
        </div>

        <!-- Table Header -->
        <div class="w-full overflow-x-auto scrollbar-hidden">
            <div class="py-4 px-6 border-b border-gray-200 dark:border-gray-700 flex items-center">
                <div class="w-4 h-4 bg-gray-200 dark:bg-gray-700 rounded mr-6"></div>
                <div class="h-5 w-32 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                <div class="h-5 w-24 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                <div class="h-5 w-28 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                <div class="h-5 w-20 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                <div class="h-5 w-24 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                <div class="h-5 w-20 bg-gray-200 dark:bg-gray-700 rounded"></div>
            </div>

            <!-- Table Rows -->
            @foreach (range(1, 4) as $i)
                <div class="py-5 px-6 border-b border-gray-200 dark:border-gray-700 flex items-center">
                    <div class="w-4 h-4 bg-gray-200 dark:bg-gray-700 rounded mr-6"></div>
                    <div class="flex-1 flex items-center">
                        <div class="w-6 h-6 bg-gray-200 dark:bg-gray-700 rounded mr-3"></div>
                        <div class="h-5 w-40 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                    <div class="h-5 w-24 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                    <div class="h-5 w-28 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                    <div class="h-5 w-20 bg-gray-200 dark:bg-gray-700 rounded mr-8"></div>
                    <div class="h-6 w-24 bg-gray-200 dark:bg-gray-700 rounded-full mr-8"></div>
                    <div class="h-5 w-8 bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Skeleton -->
        <div class="p-4 border-t border-slate-200 flex justify-between">
            <div class="h-8 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
            <div class="flex gap-2">
                @foreach (range(1, 3) as $i)
                    <div class="h-8 w-8 bg-gray-200 dark:bg-gray-700 rounded"></div>
                @endforeach
            </div>
        </div>
    </section>
</div>
