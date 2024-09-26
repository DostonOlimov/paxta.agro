<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-file-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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

        // file_path status = 1 user_id created_at
        
       // $queueFiles = find()->where(status, QueueFile::STATUS_WAITING)->get();
            // foreach($queuFileas as $file)
            // $file['path']
            // 
    }
}
