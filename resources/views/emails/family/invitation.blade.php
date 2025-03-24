@component('mail::message')
    # Invitation à rejoindre une famille

    **{{ $inviter->name }}** vous invite à rejoindre la famille **{{ $family->name }}** sur FamilyFinance.

    Dans cette famille, vous aurez le rôle de **{{ $role }}** avec la relation **{{ $relation }}**.

    @component('mail::button', ['url' => $url])
        Accepter l'invitation
    @endcomponent

    Cette invitation est valide pendant 7 jours.

    Merci,
    {{ config('app.name') }}
@endcomponent
