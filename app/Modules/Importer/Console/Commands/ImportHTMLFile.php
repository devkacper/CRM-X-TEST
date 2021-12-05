<?php

namespace App\Modules\Importer\Console\Commands;

use App\Modules\Importer\Http\Helpers\ImporterHelper;
use Illuminate\Console\Command;

class ImportHTMLFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:html';

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
        $file = 'work_orders.html';
        $import = ImporterHelper::import($file);

        echo 'Import complete! Entries created: '.$import['entriesCreated'].' Entries processed: '.$import['entriesProcessed'].PHP_EOL;
    }
}
