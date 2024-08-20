<?php

use function Livewire\Volt\layout;

layout('layouts.app');

?>

<div>
    <p>Vous êtes connecté(e) !</p>
    <p>Bienvenue sur votre tableau de bord.</p>

    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" />
</div>
