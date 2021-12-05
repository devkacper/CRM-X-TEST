<?php

namespace App\Modules\Importer\Http\Controllers;

use App\Modules\Importer\Http\Requests\FileRequest;
use App\Modules\Importer\Models\ImporterLog;
use App\Modules\WorkOrder\Models\WorkOrder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPHtmlParser\Dom;
use Illuminate\Support\Carbon;

class ImporterController extends Controller
{
    /**
     * Counters of entries.
     */
    public function  __construct()
    {
        $this->entriesCreated = 0;
        $this->entriesProcessed = 0;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('importer::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('importer::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(FileRequest $request)
    {
        // Save requested file in storage directory.
        $file = $request->file('htmlFile')->store('/');

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

                    $this->entriesCreated++;
                }
                $this->entriesProcessed++;
            }
        }

        // Store import log in database.
        $importerLog                    = new ImporterLog();
        $importerLog->type              = 'Import';
        $importerLog->run_at            = Carbon::now();
        $importerLog->entries_created   = $this->entriesCreated;
        $importerLog->entries_processed = $this->entriesProcessed;
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

        return redirect()
            ->back()
            ->with([
                'success' => 'Import complete! Entries created: '.$this->entriesCreated.' Entries processed: '.$this->entriesProcessed,
                'download' => $csvFileName
            ]
        );
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('importer::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('importer::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
