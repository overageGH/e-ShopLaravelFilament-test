<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerificationRequest;

class VerificationRequestPolicy
{
    public function view(User $user, VerificationRequest $verificationRequest): bool
    {
        return $user->isAdmin() || $verificationRequest->user_id === $user->id;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
