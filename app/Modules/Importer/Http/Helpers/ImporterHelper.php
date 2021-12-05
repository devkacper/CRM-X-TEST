<?php

namespace App\Modules\Importer\Http\Helpers;

use App\Modules\Importer\Models\ImporterLog;
use App\Modules\WorkOrder\Models\WorkOrder;
use Illuminate\Support\Carbon;
use PHPHtmlParser\Dom;

class ImporterHelper
{
    /**
     * Import file.
     */
    public function import($file)
    {
        $entriesCreated = 0;
        $entriesProcessed = 0;

        // Load requested HTML document.
        $dom = new Dom();
        $dom->loadStr(file_get_contents(storage_path('app/local/'.$file)));

        // Get necessary informations from HTML file & store in database.
        $table = $dom->find('#ctl00_ctl00_ContentPlaceHolderMain_MainContent_TicketLists_AllTickets_ctl00')[0];

        foreach($table->find('tr') as $tr) {

            $entityID = null;
            $ticket = null;
            $rcvdDate = null;
            $category = null;
            $urgency = null;
            $store = null;
            $a = $tr->find('td a')[0];

            if($a){
                $href = $a->getAttribute('href');
                $tmp = explode('entityid=', $href);

                if(isset($tmp[1])){
                    $entityID = $tmp[1];
                    $ticket = $a->getAttribute('title');
                }
            }

            $rcvdDateRaw = $tr->find('td span span.lbl')[0];

            if($rcvdDateRaw){
                $rcvdDate = $rcvdDateRaw->text;
            }

            $urgencyRaw = $tr->find('td')[3];

            if($urgencyRaw) {
                $urgency = $urgencyRaw->text;
            }

            $categoryRaw = $tr->find('td')[8];

            if($categoryRaw) {
                $category = $categoryRaw->text;
            }

            $storeRaw = $tr->find('td')[10];

            if($storeRaw) {
                $store = $storeRaw->text;
            }

            if($entityID) {
                if(!WorkOrder::where('external_id', $entityID)->exists()) {
                    $workOrder                      = new WorkOrder();
                    $workOrder->work_order_number   = $ticket;
                    $workOrder->external_id         = $entityID;
                    $workOrder->priority            = $urgency;
                    $workOrder->received_date       = Carbon::createFromFormat('d/m/Y', $rcvdDate)->format('Y-m-d H:i:s');
                    $workOrder->category            = $category;
                    $workOrder->fin_loc             = $store;
                    $workOrder->save();

                    $entriesCreated++;
                }
                $entriesProcessed++;
            }
        }

        // Store import log in database.
        $importerLog                    = new ImporterLog();
        $importerLog->type              = 'Import';
        $importerLog->run_at            = Carbon::now();
        $importerLog->entries_created   = $entriesCreated;
        $importerLog->entries_processed = $entriesProcessed;
        $importerLog->save();

        // Return CSV file with raport of imported data.
        $csvFileName = str_replace('html', 'csv', $file);

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Type', 'Run at', 'Entries created', 'Entries processed');

        $csvFile = fopen(storage_path('app/public/'.$csvFileName), 'w');
        fputcsv($csvFile, $columns);

        $row['Type'] = $importerLog->type;
        $row['Run at'] = $importerLog->run_at;
        $row['Entries created'] = $importerLog->entries_created;
        $row['Entries processed'] = $importerLog->entries_processed;

        fputcsv($csvFile, array($row['Type'], $row['Run at'], $row['Entries created'], $row['Entries processed']));

        fclose($csvFile);

        return ['entriesCreated' => $entriesCreated, 'entriesProcessed' => $entriesProcessed, 'file' => $csvFileName];
    }
}