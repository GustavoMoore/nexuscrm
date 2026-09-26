<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->isAdm() || ($project->archived_at === null && $project->users()->whereKey($user->id)->exists());
    }

    public function create(User $user): bool
    {
        return $user->isAdm();
    }

    public function update(User $user, Project $project): bool
    {
        return $user->isAdm();
    }

    public function archive(User $user, Project $project): bool
    {
        return $user->isAdm();
    }

    public function unarchive(User $user, Project $project): bool
    {
        return $user->isAdm();
    }
}
