<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequerimientoAvance;
use Illuminate\Support\Facades\Storage;

class FixEvidenceUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:evidence-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the URLs of existing evidence files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing evidence URLs...');

        $avances = RequerimientoAvance::whereNotNull('ruta_evidencias')->get();

        foreach ($avances as $avance) {
            $evidencias = json_decode($avance->ruta_evidencias, true);
            $updated = false;

            foreach ($evidencias as &$evidencia) {
                if (isset($evidencia['path'])) {
                    $newUrl = Storage::disk('public')->url($evidencia['path']);
                    if (!isset($evidencia['url']) || $evidencia['url'] !== $newUrl) {
                        $evidencia['url'] = $newUrl;
                        $updated = true;
                    }
                }
            }

            if ($updated) {
                $avance->ruta_evidencias = json_encode($evidencias);
                $avance->save();
            }
        }

        $this->info('Done.');
    }
}
