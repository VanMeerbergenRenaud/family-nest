<x-guest-layout>
    <div class="m-4 flex flex-col gap-2">
        <h1 role="heading" aria-level="1">FamilyNest</h1>

        <p>La meilleure application pour gérer les factures et les dépenses de votre famille.</p>

        <p>Commencer par vous inscrire ou vous connecter pour accéder à l'application.</p>

        <div class="inline-flex gap-6">
            <a href="{{ route('register') }}" title="Vers la page d’inscription">S’inscrire</a>
            <a href="{{ route('login') }}" title="Vers la page de connexion">Se connecter</a>
        </div>
    </div>
</x-guest-layout>
