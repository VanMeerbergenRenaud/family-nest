@component('mail::message')
# Bonjour !

Merci de vous être inscrit sur FamilyNest. Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse email.

@component('mail::button', ['url' => $url])
Vérifier mon adresse email
@endcomponent

Si vous n'avez pas créé de compte, aucune action supplémentaire n'est requise.

Cordialement,<br>
{{ config('app.name') }}
@endcomponent
