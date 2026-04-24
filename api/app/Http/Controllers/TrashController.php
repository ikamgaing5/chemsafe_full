<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Trash;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function workshop(){
        $allWorkshops = Trash::whereNotNull('atelier_id')->with(['atelier', 'userr'])->get()->map(function ($item) {
            $item->jours_restants = max(0, 30 - Carbon::parse($item->created_at)->diffInDays(now()));
            return $item;
        });

        return response()->json([
            'message' => 'Corbeille des ateliers.',
            'workshop' => $allWorkshops
        ]);
    }

    public function factories(){
        $allfactories = Trash::whereNotNull('usine_id')->with(['usine', 'userr'])->get()->map(function ($item) {
            $item->jours_restants = max(0, 30 - Carbon::parse($item->created_at)->diffInDays(now()));
            return $item;
        });

        return response()->json([
            'message' => 'Corbeille des usines.',
            'factories' => $allfactories
        ]);
    }

    
}
