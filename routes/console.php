<?php

use App\Enums\FamilyPermissionEnum;
use App\Enums\FamilyRelationEnum;
use App\Jobs\SendFamilyInvitation;
use App\Mail\AuthVerifyEmail;
use App\Mail\PaymentReminder;
use App\Models\Family;
use App\Models\FamilyInvitation;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

/*
 * Command to send a test email
 * $ php artisan mail:test {email}
*/
Artisan::command('mail:test {email}', function ($email) {
    \Mail::raw('Test from FamilyNest', function ($message) use ($email) {
        $message->to($email)->subject('Test Email');
    });
    $this->info("Nickel l'email à été envoyé à {$email}");
});

/*
 * Command to test sending a verification email
 * $ php artisan mail:test-verification {email?}
*/
Artisan::command('mail:test-verification {email?}', function ($email = null) {
    if (! $email) {
        $email = $this->ask('Entrez l\'email du destinataire');
    }

    // Chercher un utilisateur avec cet email ou créer un utilisateur temporaire
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->info("Aucun utilisateur trouvé avec l'email $email. Création d'un utilisateur temporaire...");
        $user = new User([
            'name' => 'Utilisateur non vérifié',
            'email' => $email,
            'password' => Hash::make(\Illuminate\Support\Str::random(16)),
            'email_verified_at' => null,
        ]);
    } else {
        $this->info("Utilisateur trouvé: {$user->name}");
    }

    $this->info("Envoi de l'email de vérification à $email...");

    \Mail::to($email)->send(new AuthVerifyEmail($user));

    $this->info("✅ Email de vérification envoyé avec succès à $email!");
    $this->newLine();
    $this->table(
        ['Propriété', 'Valeur'],
        [
            ['Destinataire', $email],
            ['Nom utilisateur', $user->name],
            ['URL de vérification', 'URL temporaire signée générée automatiquement'],
            ['Expiration', config('auth.verification.expire', 60).' minutes'],
        ]
    );

    if (! $user->exists) {
        $this->warn("Note: L'utilisateur temporaire n'a pas été enregistré en base de données.");
    }

    return 0;
})->purpose('Envoie un email test de vérification d\'adresse email');

/*
 * Command to test sending a family invitation email
 * $ php artisan mail:test-invitation {email?}
*/
Artisan::command('mail:test-invitation {email?}', function ($email = null) {
    if (! $email) {
        $email = $this->ask('Entrez l\'email du destinataire');
    }

    // Récupérer une famille existante ou créer une famille de test
    $family = Family::first();
    if (! $family) {
        $this->error('Aucune famille trouvée dans la base de données!');

        return 1;
    }

    // Récupérer l'utilisateur qui envoie l'invitation (admin de la famille)
    $inviter = User::whereHas('families', function ($query) use ($family) {
        $query->where('families.id', $family->id)
            ->where('permission', \App\Enums\FamilyPermissionEnum::Admin->value);
    })->first();

    if (! $inviter) {
        $inviter = User::first();
        if (! $inviter) {
            $this->error('Aucun utilisateur trouvé pour envoyer l\'invitation!');

            return 1;
        }
    }

    // Créer une invitation temporaire pour le test
    $invitation = new FamilyInvitation([
        'email' => $email,
        'token' => \Illuminate\Support\Str::random(64),
        'family_id' => $family->id,
        'permission' => FamilyPermissionEnum::Viewer->value,
        'relation' => FamilyRelationEnum::Member->value,
        'invited_by' => $inviter->id,
        'expires_at' => now()->addDays(7),
    ]);
    $invitation->save();

    $this->info("Envoi de l'invitation à la famille \"{$family->name}\"...");

    // Dispatche le job d'invitation
    SendFamilyInvitation::dispatch(
        $invitation,
        $family,
        $inviter
    );

    $this->info("✅ Invitation à la famille envoyée avec succès à $email!");
    $this->newLine();
    $this->table(
        ['Propriété', 'Valeur'],
        [
            ['Famille', $family->name],
            ['Invité par', $inviter->name],
            ['Email destinataire', $email],
            ['Permission', FamilyPermissionEnum::tryFrom($invitation->permission)?->label() ?? $invitation->permission],
            ['Relation', FamilyRelationEnum::tryFrom($invitation->relation)?->label() ?? $invitation->relation],
            ['Expire le', $invitation->expires_at->format('d/m/Y')],
        ]
    );

    return 0;
})->purpose('Envoie un email test d\'invitation à une famille');

/*
 * Command to test payment reminder emails (optional?)
 * $ php artisan invoice:test-reminder {invoice_id?} {email?}
*/
Artisan::command('invoice:test-reminder {invoice_id?} {email?}', callback: function ($invoiceId = null, $email = null) {
    if (! $invoiceId) {
        $invoiceId = $this->ask('Entrez l\'ID de la facture');
    }

    $invoice = Invoice::find($invoiceId);

    if (! $invoice) {
        $this->error("Facture avec l'ID $invoiceId non trouvée!");

        return 1;
    }

    $user = User::find($invoice->user_id);

    if (! $user) {
        $this->error('Utilisateur associé à la facture non trouvé!');

        return 1;
    }

    if ($email) {
        $emailToUse = $email;
        $this->info("Utilisation de l'email spécifié: $emailToUse");
    } else {
        $emailToUse = $user->email;
        $this->info("Utilisation de l'email de l'utilisateur: $emailToUse");
    }

    $invoiceUrl = route('invoices.show', ['id' => $invoice->id]);

    $this->info("Envoi du rappel de paiement pour la facture \"{$invoice->name}\"...");

    \Illuminate\Support\Facades\Mail::to($emailToUse)
        ->send(new PaymentReminder(
            $invoice,
            $user,
            $invoiceUrl
        ));

    $this->info("✅ Email de rappel envoyé avec succès à $emailToUse!");
    $this->newLine();
    $this->table(
        ['Propriété', 'Valeur'],
        [
            ['ID Facture', $invoice->id],
            ['Nom Facture', $invoice->name],
            ['Montant', number_format($invoice->amount, 2, ',', ' ').' '.($invoice->symbol ?? '€')],
            ['Date d\'échéance', $invoice->payment_due_date?->format('d/m/Y') ?: 'Non spécifiée'],
            ['Email destinataire', $emailToUse],
        ]
    );

    return 0;
})->purpose('Envoie un email de test pour le rappel de paiement d\'une facture');
