@component('mail::message')
#Inventory-Gestion de Stock
Bonjour**{{ $notifiable->name }}**,
Une nouvelle action concernant le stock a été enregistrée dans le système :
@component('mail::panel')
{{ $message }}
@endcomponent
Vous pouvez consulter les détails et mettre à jour l'inventaire en cliquant sur le bouton ci-dessous :
@component('mail::button', ['url' => $url, 'color' => 'primary'])
Voir mon Stock
@endcomponent
Merci de votre collaboration,<br>
L'équipe technique **{{ config('app.name') }}**
@endcomponent