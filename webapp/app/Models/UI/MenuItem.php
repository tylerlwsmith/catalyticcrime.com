<?php

namespace App\Models\UI;

use Illuminate\Support\Facades\Route;

class MenuItem
{
    public $name;
    public $routeName;

    public function __construct($name, $routeName)
    {
        if (!Route::has($routeName)) throw new \Exception(
            "Route \"$routeName\" doesn't exist on MenuItem ${name}."
        );

        $this->name = $name;
        $this->routeName = $routeName;
    }
}
