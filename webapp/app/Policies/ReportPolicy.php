<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // https://laravel.com/docs/8.x/authorization#policy-filters
    public function before(User $user, $ability)
    {
        if ($user->is_admin) return true;
    }

    public function show(?User $user, Report $report)
    {
        if ($report->admin_approved) return true;
        if (!$user) return false;
        return $report->user_id === $user->id;
    }

    public function edit(User $user, Report $report)
    {
        return $report->user_id === $user->id;
    }
}
