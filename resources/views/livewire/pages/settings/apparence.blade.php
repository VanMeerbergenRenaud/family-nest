<div>
    @if(auth()->check() && auth()->user()->email === 'dominique.vilain@gmail.com')
        <x-header title="Apparence"
                  description=" Modifiez l'apparence et la convivialité de votre tableau de bord."
                  class="mb-5"
        />

        <livewire:pages.settings.style-customizer />
    @else
        <x-coming-soon title="Apparence" />
    @endif
</div>
