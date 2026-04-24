<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Atelier;
use App\Models\Usine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AtelierController extends Controller
{
    public $search;
    public function allByFactory($code, Request $request)
    {
        $search = $request->query('search', '');
        $usine = Usine::where('code', $code)->first();
        $all = Atelier::where('usine_id', function ($query) use ($code) {
            $query->select('id')
                ->from('usine')
                ->where('code', $code);
        })
            ->where('active', 'true')
            ->withCount([
                'produits',
                'produitSansFds as sans_fds'
            ])
            ->orderBy('nomatelier', 'asc')
            ->where('nomatelier', 'like', "%$search%")
            ->paginate(5);
        return response()->json([
            'ateliers' => $all,
            'message' => 'Liste des ateliers de l\'' . $usine->nomusine . '.',
            'usine' => $usine
        ]);
    }

    protected function rules($id = null)
    {
        $rules = [
            'nomatelier' => ['required', 'string', 'max:255'],
        ];
        $rules['nom'][] = Rule::unique('atelier', 'nomatelier')->where(function ($query) use ($id) {
            return $query->where('usine_id', $id);
        });
        $rules['usine_id'] = 'required|exists:usine,id';

        return $rules;
    }

    public function add(Request $request, $idUsine)
    {
        $data = $request->validate($this->rules($idUsine));

        $ateliers = Atelier::create($data);
        return response()->json([
            'information' => "L'$ateliers->nomatelier a bien été ajouté.",
            'atelier' => [
                ...$ateliers->only([
                    'id',
                    'nomatelier',
                    'usine_id',
                    'code'
                ]),
                'produits_count' => 0,
                'sans_fds' => 0
            ]
        ]);
    }

    public function edit(Request $request, Atelier $atelier)
    {
        $data = $request->validate([
            'nomatelier' => 'required|string|max:255',
        ]);

        $atelier = atelier::withCount([
            'produits as produits_count' => function ($query) {
                $query->where('active', 'true');
            },
            'produitSansFds as sans_fds' => function ($query) {
                $query->where('active', 'true');
            }
        ])->findOrFail($request->id);
        $atelier->update($data);
        return response()->json([
            'message' => 'Atelier mis à jour avec succès.',
            'atelier' => [
                ...$atelier->only([
                    'id',
                    'nomatelier'
                ]),
                'produits_count' => $atelier->produits_count,
                'sans_fds' => $atelier->sans_fds
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        try {
            $search = trim(request('search', ''));
            $perPage = (int) request('perPage', 10);

            $atelierCount = fn($q) => $q
                ->where('active', 'true')
                ->when($search, fn($q) => $q->where('nomatelier', 'like', "%{$search}%"));

            $avecPaginator = Usine::select(['id', 'nomusine', 'code'])
                ->where('active', 'true')
                ->withCount(['ateliers as count' => $atelierCount])
                ->whereHas('ateliers', $atelierCount)
                ->orderBy('nomusine', 'asc')
                ->paginate($perPage);

            $sansAtelier = Usine::select(['id', 'nomusine', 'code'])
                ->where('active', 'true')
                ->whereDoesntHave('ateliers', fn($q) => $q->where('active', 'true'))
                ->orderBy('nomusine', 'asc')
                ->get();

            return response()->json([
                'data' => [
                    'usineAvecAtelier' => $avecPaginator->items(),
                    'usineSansAtelier' => $sansAtelier,
                ],
                'meta' => [
                    'total' => $avecPaginator->total(),
                    'per_page' => $avecPaginator->perPage(),
                    'current_page' => $avecPaginator->currentPage(),
                    'last_page' => $avecPaginator->lastPage(),
                ],
                'message' => 'Liste des usines.',
            ]);

        } catch (\Exception $e) {
            Log::error('Usine::all() failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }

    public function ateliersByUsine(int $usineId): JsonResponse
    {
        try {
            $search = trim(request('search', ''));
            $perPage = (int) request('perPage', 6);

            $paginator = Atelier::select(['id', 'usine_id', 'nomatelier', 'code'])
                ->where('active', 'true')
                ->where('usine_id', $usineId)
                ->withCount(['produits', 'produitSansFds as sans_fds'])
                ->when($search, fn($q) => $q->where('nomatelier', 'like', "%{$search}%"))
                ->orderBy('nomatelier', 'asc')
                ->paginate($perPage);

            return response()->json([
                'data' => $paginator->items(),
                'meta' => [
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Usine::ateliersByUsine() failed', [
                'error' => $e->getMessage(),
                'usine_id' => $usineId,
            ]);
            return response()->json(['message' => 'Une erreur est survenue.'], 500);
        }
    }


    
}
