<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PizzaController extends Controller
{

    public function index(){
        $pizzas = Pizza::all();

        return Inertia::render('Pizzas/All', [
            'pizzas' => $pizzas,]);

    }

    public function edit(Pizza $pizza): Response {
        return Inertia::render('Pizzas/Edit', [
            'pizza' => $pizza
        ]);
    }

    public function update(Request $request, Pizza $pizza): void {
        $pizza->update([
            'status' => $request->status,
        ]);
}
}
