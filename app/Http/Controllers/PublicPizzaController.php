<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPizzaController extends Controller
{
   public function index(Pizza $pizza): Response
    {

    return Inertia::render('Pizzas/show', [
        'pizza' => $pizza,
    ]);
    }
}
