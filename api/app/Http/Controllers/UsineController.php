<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Usine;
use Illuminate\Http\Request;

class UsineController extends Controller
{
    public function all()
    {
        $all = Usine::where('active', 'true')
            ->withCount([
                'ateliers as count' => function ($query) {
                    $query->where('active', 'true');
                }
            ])
            ->with('users')
            ->orderBy('nomusine', 'asc')
            ->get();

        return response()->json([
            'usines' => $all,
            'message' => 'Liste des usines.'
        ]);

    }

    public function alls()
    {
        $all = Usine::where('active', 'true')
            ->with([
                'ateliers' => function ($query) {
                    $query->where('active', 'true');
                }
            ])
            ->orderBy('nomusine', 'asc')
            ->get();

        return response()->json([
            'usines' => $all,
            'role' => auth()->user()->role,
        ]);

    }

    public function edit(Request $request, Usine $usine)
    {
        $data = $request->validate([
            'nomusine' => 'required|string|max:255',
        ]);

        $usine = Usine::withCount([
                'ateliers as count' => function ($query) {
                    $query->where('active', 'true');
                }
            ])->findOrFail($request->id);
        $usine->update($data);
        return response()->json([
            'message' => 'Usine mis à jour avec succès.',
            'usine' => [...$usine->only([
                'id',
                'nomusine'
            ]),
            'count' => $usine->count
            ],
        ]);
    }

    public function add(Request $request)
    {
        $data = $request->validate([
            'nomusine' => 'required|string|max:255|unique:usine,nomusine',
        ], [
            'nomusine.required' => 'Le nom est obligatoire.',
            'nomusine.unique' => 'Ce nom est déjà utilisé. Veuillez en choisir un autre.'
        ]);
        $usine = Usine::create($data);
        return response()->json([
            'information' => "L'$usine->nomusine a bien été ajouté.",
            'usine' => $usine
        ]);
    }

    public function delete($id)
    {
        $usine = Usine::findOrFail($id);
        if ($usine->ateliers->where('active', 'true')->count() > 0) {
            return response()->json([
                'error' => "Cette usine contient des ateliers actifs et ne peut être supprimée."
            ], 422);
        }
        $usine->update(['active' => 'false']);
        return response()->json(null, 200);

    }

}
