@props(['results'])

<ul>
    @foreach($results as $section => $items)
        <li wire:key="{{ $section }}">
            <x-divider class="dark:bg-gray-700"/>

            <div class="bg-white dark:bg-gray-800 py-3 px-2">
                <p class="text-sm-medium text-gray-500 dark:text-gray-400 mb-2 pl-2.5">
                    {{ $section }}
                </p>
                <ul role="list" class="flex flex-col gap-1">
                    @foreach ($items as $result)
                        @if ($result instanceof \App\Models\Invoice)
                            <x-spotlight.result-item
                                :id="$result->id"
                                type="invoice"
                                :href="route('invoices.show', $result)"
                                :text="$result->name"
                                :description="'('.$result->amount.' '.$result->currency.')'"
                                :state="$result->is_archived ? '#archivée' : null"
                            >
                                <x-svg.invoice class="h-5 w-5 group-hover:text-gray-800" />
                            </x-spotlight.result-item>
                        @endif

                        @if ($result instanceof \App\Models\User)
                            <x-spotlight.result-item
                                :id="$result->id"
                                type="user"
                                :href="route('settings.profile')"
                                :text="$result->name"
                                :description="$result->email"
                                :state="$result->getFamilyPermissionAttribute()"
                            >
                                <img
                                    class="h-6 w-6 rounded-full bg-gray-100 dark:bg-gray-700"
                                    src="{{ $result->avatar_url ?? asset('img/avatar_placeholder.png') }}"
                                    alt="{{ $result->name }}"/>
                            </x-spotlight.result-item>
                        @endif
                    @endforeach
                </ul>
            </div>
        </li>
    @endforeach
</ul>
