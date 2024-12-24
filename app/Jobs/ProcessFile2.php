<?php

namespace App\Jobs;

use App\Models\ClampData;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use XBase\TableReader;

class ProcessFile2 implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $balles;
    protected $gin_id;

    public function __construct($array)
    {

        $this->path = $array['path'];
        $this->balles = $array['balles'];
        $this->gin_id = $array['gin_id'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Load the spreadsheet file
        $file = storage_path('app/' . $this->path);
        $spreadsheet = IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();

        // Parse Excel data into an array
        $excelData = $this->parseWorksheet($worksheet);

        // Retrieve existing clamped data
        $clampedData = $this->getClampedData();

        // Process the data and prepare for insertion or updates
        [$dataToInsert, $dataToUpdate] = $this->processExcelData($excelData, $clampedData);

        // Perform bulk insertion for new records
        if (!empty($dataToInsert)) {
            ClampData::insert($dataToInsert);
        }

        // Update existing records
        foreach ($dataToUpdate as $update) {
            $this->updateClampedData($update);
        }
    }

    /**
     * Parse the worksheet to extract data.
     */
    private function parseWorksheet($worksheet)
    {
        $data = [];
        foreach ($worksheet->getRowIterator() as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = $cell->getValue();
            }
            $data[] = $rowData;
        }
        return $data;
    }

    /**
     * Retrieve clamped data from the database.
     */
    private function getClampedData()
    {
        return ClampData::whereIn('gin_bale', range($this->balles->from_number, $this->balles->to_number))
            ->where('gin_id', $this->gin_id)
            ->where('dalolatnoma_id', $this->balles->dalolatnoma_id)
            ->pluck('gin_bale')
            ->toArray();
    }

    /**
     * Process Excel data to prepare for insertion and updates.
     */
    private function processExcelData(array $excelData, array $clampedData)
    {
        $dataToInsert = [];
        $dataToUpdate = [];

        foreach ($excelData as $data) {
            $ginId = $data[0] ?? null;
            $ginBale = $data[1] ?? null;

            // Skip rows with invalid gin_id or gin_bale
            if (!$ginId || !$ginBale) {
                continue;
            }

            if ($ginId == $this->gin_id && $ginBale >= $this->balles->from_number && $ginBale <= $this->balles->to_number) {
                if (!in_array($ginBale, $clampedData)) {
                    $dataToInsert[] = $this->prepareInsertData($data);
                } else {
                    $dataToUpdate[] = $this->prepareUpdateData($data);
                }
            }
        }

        return [$dataToInsert, $dataToUpdate];
    }

    /**
     * Prepare data for insertion.
     */
    private function prepareInsertData(array $data)
    {
        return [
            'dalolatnoma_id' => $this->balles->dalolatnoma_id,
            'gin_id' => $data[0],
            'gin_bale' => $data[1],
            'lot_number' => $data[2],
            'weight' => null,
            'selection' => $data[12],
            'date_class' => $data[4],
            'time_class' => $data[5],
            'classer_id' => $data[10],
            'sort' => $data[6],
            'class' => $data[7],
        ];
    }

    /**
     * Prepare data for updating existing records.
     */
    private function prepareUpdateData(array $data)
    {
        return [
            'gin_id' => $data[0],
            'gin_bale' => $data[1],
            'sort' => $data[6],
            'class' => $data[7],
            'classer_id' => $data[10],
        ];
    }

    /**
     * Update existing clamped data in the database.
     */
    private function updateClampedData(array $data)
    {
        $clamp = ClampData::where('gin_bale', $data['gin_bale'])
            ->where('gin_id', $data['gin_id'])
            ->where('dalolatnoma_id', $this->balles->dalolatnoma_id)
            ->first();

        if ($clamp) {
            $clamp->sort = $data['sort'];
            $clamp->class = $data['class'];
            $clamp->classer_id = $data['classer_id'];
            $clamp->save();
        }
    }

}
