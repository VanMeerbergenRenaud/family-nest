<?php

namespace App\Livewire\Pages\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class StyleCustomizer extends Component
{
    public bool $showCustomizer = false;

    public string $fontFamily = '';

    public string $fontSize = 'base';

    public string $zoomLevel = '100';

    public bool $isOpen = false;

    public function mount()
    {
        if (Auth::check() && Auth::user()->email === 'dominique.vilain@gmail.com') {
            $this->showCustomizer = true;

            if (Storage::exists('user_styles/vip_user.css')) {
                $this->loadSavedPreferences();
            }
        }
    }

    public function togglePanel(): void
    {
        $this->isOpen = ! $this->isOpen;
    }

    public function updateStyles(): void
    {
        if (! $this->showCustomizer) {
            return;
        }

        $css = $this->generateCustomCSS();

        Storage::put('user_styles/vip_user.css', $css);

        session()->flash('message', 'Tes styles personnalisés ont été appliqués !');

        $this->redirect(request()->header('Referer'));
    }

    private function loadSavedPreferences() {}

    private function generateCustomCSS(): string
    {
        return <<<CSS
        :root {
            --font-family: {$this->getFontFamily()};
            --font-size-base: {$this->getFontSize()};
            --zoom-level: {$this->zoomLevel}%;
        }

        body {
            font-family: var(--font-family) !important;
            font-size: var(--font-size-base) !important;
            zoom: var(--zoom-level) !important;
        }

        CSS;
    }

    private function getFontFamily()
    {
        $fonts = [
            'sans' => 'ui-sans-serif, system-ui, sans-serif',
            'serif' => 'ui-serif, Georgia, Cambria, serif',
            'comic' => '"Comic Sans MS", cursive',
            'apple' => '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif',
        ];

        return $fonts[$this->fontFamily] ?? $fonts['sans'];
    }

    private function getFontSize(): string
    {
        $sizes = [
            'sm' => '0.9rem',
            'base' => '1rem',
            'lg' => '1.1rem',
            'xl' => '1.25rem',
        ];

        return $sizes[$this->fontSize] ?? $sizes['base'];
    }

    public function resetStyles(): void
    {
        if (Storage::exists('user_styles/vip_user.css')) {
            Storage::delete('user_styles/vip_user.css');
        }

        $this->fontFamily = 'sans';
        $this->fontSize = 'base';
        $this->zoomLevel = '100';

        Toaster::success('Styles réinitialisés aux valeurs par défaut');

        $this->redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.pages.settings.style-customizer')
            ->layout('layouts.app-sidebar');
    }
}
