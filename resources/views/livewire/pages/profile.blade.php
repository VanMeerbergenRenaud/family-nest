<?php

use function Livewire\Volt\layout;
use Livewire\Volt\Component;

layout('layouts.app');

?>

<div>
    <livewire:profile.update-profile-information-form />
    <livewire:profile.update-password-form />
    <livewire:profile.delete-user-form />
</div>
