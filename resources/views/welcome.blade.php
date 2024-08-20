<x-guest-layout>
    <div>
        <h1 role="heading" aria-level="1">FamilyNest</h1>
        <p>La meilleure application pour gérer les factures et les dépenses de votre famille.</p>
    </div>

    <p>
        Commencer par vous inscrire ou vous connecter pour accéder à l'application.
    </p>

    <div>
        <a href="{{ route('register') }}" title="Vers la page d’inscription">S’inscrire</a>
        <a href="{{ route('login') }}" title="Vers la page de connexion">Se connecter</a>
    </div>
</x-guest-layout>
