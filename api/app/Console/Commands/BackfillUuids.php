<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillUuids extends Command
{
    protected $signature = 'uuid:backfill';
    protected $description = 'Génère des UUID pour toutes les tables concernées';

    public function handle(): void
    {
        $models = [
            // \App\Models\Usine::class,
            // \App\Models\Atelier::class,
            \App\Models\Produit::class,
            // \App\Models\Danger::class,
            \App\Models\FDS::class,
            // \App\Models\historique_acces::class,
            // \App\Models\Historique::class,
            // \App\Models\Trash::class,
            // \App\Models\User::class, // exemple
        ];

        foreach ($models as $model) {
            $this->info("Traitement : {$model}");
            $i = 0;

            $model::chunkById(100, function ($items) use ($i) {
                foreach ($items as $item) {
                    if (!Str::isUuid($item->code)) {
                        $item->code = Str::uuid();
                        $item->saveQuietly();
                        $i++;
                    }
                    // $item->code = Str::uuid();
                    // $item->saveQuietly();
                    // $i++;
                    // $model::where('id', $item->id)
                    //     ->update(['code' => Str::uuid()]);
                    // $i++;
                }
            });
            $this->info("$i uuid crée pour {$model}.");
        }

        $this->info('✅ UUID générés pour toutes les tables');
    }
}
