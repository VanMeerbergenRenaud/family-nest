<?php

namespace App\Services;

use App\Models\FamilyInvitation;
use Illuminate\Support\Facades\DB;
use Masmerise\Toaster\Toaster;

class FamilyService
{
    /**
     * Checks if an email is already a member or has a pending invitation
     */
    public function memberOrInvitationExists(int $familyId, string $email): bool
    {
        // Check if the user is already a member
        $isMember = DB::table('users')
            ->join('family_user', 'users.id', '=', 'family_user.user_id')
            ->where('family_user.family_id', $familyId)
            ->where('users.email', $email)
            ->exists();

        if ($isMember) {
            Toaster::error('Cet utilisateur est déjà membre de la famille.');

            return true;
        }

        // Check if an invitation is already pending
        $hasInvitation = FamilyInvitation::where('family_id', $familyId)
            ->where('email', $email)
            ->exists();

        if ($hasInvitation) {
            Toaster::error('Une invitation a déjà été envoyée à cet e-mail.');

            return true;
        }

        return false;
    }

    /**
     * Prepares invitation data for sending multiple invitations
     * This method only filters and validates data, without creating records
     */
    public function prepareInvitationsData(array $members, int $familyId): array
    {
        $validMembers = [];

        foreach ($members as $member) {
            // Ignore empty or invalid entries
            if (empty($member['email']) || ! filter_var($member['email'], FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            // Avoid inviting the user themselves
            if ($member['email'] === auth()->user()->email) {
                continue;
            }

            // Check if already a member or invitation is pending
            if ($this->memberOrInvitationExists($familyId, $member['email'])) {
                continue;
            }

            $validMembers[] = $member;
        }

        return $validMembers;
    }
}
