<?php

namespace App\Listeners;

use App\Mail\UserRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendAdminRegistrationNotification implements ShouldQueue
{
    public function handle(Registered $event)
    {
        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new UserRegistered($event->user));
        }
    }
}
