{{-- Theme switcher --}}
<div x-data="{
        theme: localStorage.getItem('theme') || 'light',
        init() {
            this.applyTheme();
        },
        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
            this.applyTheme();
        },
        applyTheme() {
            localStorage.setItem('theme', this.theme);
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
        }
    }"
    {{ $attributes }}
>
    {{-- Button to toggle the theme --}}
    <button
        type="button"
        {{ $attributes->merge(['class' => 'relative transition duration-300 ease-in-out bg-gray-300 rounded-full dark:bg-gray-700']) }}
        title="Changer le thème de la page"
        aria-label="Changer le thème de la page"
        @click="toggleTheme()"
        :aria-pressed="theme === 'dark'"
    >
        {{-- Light mode --}}
        <span
            x-show="theme === 'light'"
            class="inline-flex items-center gap-x-2 py-2 px-3 bg-white/40 rounded-full text-sm text-gray-700 dark:text-white hover:bg-white/20"
            data-hs-theme-click-value="dark"
        >
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path>
            </svg>
        </span>

        {{-- Dark mode --}}
        <span
            x-show="theme === 'dark'"
            class="inline-flex items-center gap-x-2 py-2 px-3 bg-white/40 rounded-full text-sm text-white hover:bg-white/20"
            data-hs-theme-click-value="light"
        >
            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round">
                <circle cx="12" cy="12" r="4"></circle>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.93 4.93 1.41 1.41"></path>
                <path d="m17.66 17.66 1.41 1.41"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.34 17.66-1.41 1.41"></path>
                <path d="m19.07 4.93-1.41 1.41"></path>
            </svg>
        </span>
    </button>
</div>
