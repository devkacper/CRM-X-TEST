<?php

namespace App\Modules\Importer\Console\Commands;

use App\Modules\Importer\Http\Helpers\ImporterHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;

class ImportHTMLFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:html {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import HTML file and store informations in database. Return csv file with import log.';

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
        try {
            $filePath   = $this->option('path');
            $file       = Storage::putFile('/', new File($filePath));
            $import     = ImporterHelper::import($file);

            echo 'Import complete! Entries created: '.$import['entriesCreated'].' Entries processed: '.$import['entriesProcessed'].PHP_EOL;
        } catch(\Exception $e) {
            echo 'Sorry, looks like something went wrong.'.PHP_EOL;
        }

    }
}
