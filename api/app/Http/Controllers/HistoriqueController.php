<?php

namespace App\Http\Controllers;

use App\Models\Historique;
// use Illuminate\Http\Request;

class HistoriqueController extends Controller
{
    public function atelier()
    {
        $allWorkshops = Historique::whereNotNull('atelier_id')->with('userr', 'atelier')->get();

        return response()->json([
            'all' => $allWorkshops,
            'message' => "Historique des ateliers."
        ]);
    }

    public function usine()
    {
        $allfactories = Historique::whereNotNull('usine_id')->with('userr', 'usine')->get();

        return response()->json([
            'all' => $allfactories,
            'message' => "Historique des usines."
        ]);
    }

    public function user()
    {
        $allusers = Historique::whereNotNull('user_id')->with('user','userr', 'atelier','usine')->get();

        return response()->json([
            'all' => $allusers,
            'message' => "Historique des utilisateurs."
        ]);
    }
}
