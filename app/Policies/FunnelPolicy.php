<?php

namespace App\Policies;

use App\Models\Funnel;
use App\Models\User;

class FunnelPolicy
{
    public function view(User $user, Funnel $funnel): bool
    {
        return (new ProjectPolicy)->view($user, $funnel->project)
            && ($user->isAdm() || $funnel->archived_at === null);
    }

    public function create(User $user): bool
    {
        return $user->isAdm();
    }

    public function update(User $user, Funnel $funnel): bool
    {
        return $user->isAdm();
    }

    public function archive(User $user, Funnel $funnel): bool
    {
        return $user->isAdm();
    }

    public function unarchive(User $user, Funnel $funnel): bool
    {
        return $user->isAdm();
    }
}
