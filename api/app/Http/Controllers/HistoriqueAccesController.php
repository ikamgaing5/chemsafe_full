<?php

namespace App\Http\Controllers;

// use App\Models\historique_acces;
// use Illuminate\Http\Request;
use App\Models\User;

class HistoriqueAccesController extends Controller
{
    public function all(){
        $all = User::with('historique_acces')->orderBy('id')->get();

        return response()->json([
                'all' => $all,
                'message' => 'Historique de connexion.'
            ]);
    }
}
