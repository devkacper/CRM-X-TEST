<?php

namespace App\Modules\Importer\Http\Controllers;

use App\Modules\Importer\Http\Requests\FileRequest;
use App\Modules\WorkOrder\Models\WorkOrder;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PHPHtmlParser\Dom;
use Illuminate\Support\Carbon;

class ImporterController extends Controller
{
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
        $file = $request->file('htmlFile');
        $tmpFile = file_get_contents('../storage/work_orders.html');

        $dom = new Dom();
        $dom->loadStr($tmpFile);

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
                WorkOrder::create([
                    'external_id'       => $entityID,
                    'work_order_number' => $ticket,
                    'priority'          => $urgency,
                    'received_date'     => Carbon::createFromFormat('d/m/Y', $rcvdDate)->format('Y-m-d H:i:s'),
                    'category'          => $category,
                    'fin_loc'           => $store
                ]);
            }
        }

        // 4. Store import log in database.
        // 5. Return CSV file with raport of imported data.

        // *6. Add console command to import file with the same functionality like in web interface.
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
