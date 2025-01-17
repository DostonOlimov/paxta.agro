<?php

namespace App\Console\Commands;

use App\Models\ChigitTips;
use App\Models\SifatSertificates;
use Illuminate\Console\Command;

class UpdateSertificateValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sertificates:update-values';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update calculated values for all sertificates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SifatSertificates::all()->each(function ($sertificate) {
            $application = $sertificate->application;

            if ($application) {
                $quality = 0;
                $nuqsondorlik = $application->chigit_result()->where('indicator_id', 9)->value('value');
                $zararkunanda = $application->chigit_result()->where('indicator_id', 10)->value('value');
                $tukdorlik = $application->chigit_result()->where('indicator_id', 12)->value('value');
                $namlik = $application->chigit_result()->where('indicator_id', 11)->value('value');

                $cropId = $application->crops->name_id ?? null;

                $tipQuery = ChigitTips::where('nuqsondorlik', '>=', $nuqsondorlik)
                    ->where('crop_id', $cropId);

                if ($cropId == 2) {
                    $tipQuery->where('tukdorlik', '>=', $tukdorlik)
                        ->where('tukdorlik_min', '<=', $tukdorlik);
                }

                $tip = $tipQuery->first();

                if($tip && $namlik <= $tip->namlik
                    && $tukdorlik <= $tip->tukdorlik
                    && $tukdorlik >= $tip->tukdorlik_min){
                    $quality = 1;
                }

                $sertificate->quality = $quality;
                $sertificate->amount = $application->amount * ((100 - $namlik - $zararkunanda) / (100-10-0.5));
                $sertificate->save();
            }
        });

        $this->info('Calculated values updated successfully!');
    }
}
