<?php

namespace App\View\Components;

use App\Models\UI\MenuItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Navigation extends Component
{
    /** @var MenuItem[] */
    public $menu;

    public function __construct()
    {
        $this->menu = $this->makeMenu();
    }

    public function makeMenu()
    {
        $menu = [
            new MenuItem('Report a Theft', 'reports.create'),
            new MenuItem('About', 'about'),
        ];

        if (Auth::guest()) {
            $menu[] = new MenuItem('Login', 'login');
            $menu[] = new MenuItem('Sign Up', 'register');
        }

        return $menu;
    }

    public function render()
    {
        return view('layouts.navigation');
    }
}
