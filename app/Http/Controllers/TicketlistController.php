<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;
use App\Exports\TicketListExport;
use App\Imports\TicketListImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TicketListExportPerDay;
use App\Imports\ReqRemedySheetImport;

class TicketlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Form::join('applications', 'forms.app_id', '=', 'applications.app_id')
                ->join('kedbs', 'forms.kedb_id', '=', 'kedbs.kedb_id')
                ->join('users', 'forms.user_id', '=', 'users.user_id')
                ->select('forms.*', 'applications.app_name', 'kedbs.kedb_finalisasi', 'users.username')
                ->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($data) {
                    return $data->created_at->format('Y-m-d');
                })
                ->addColumn('option', function ($ticket) {
                    $button = '<div class="d-flex">';
                    // $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $ticket->form_id . '" data-original-title="Edit" class="btn btn-primary btn-sm editForm">Edit</a>';
                    // $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $ticket->form_id . '" data-original-title="Show" class="btn btn-info btn-sm showForm">Show</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $ticket->form_id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteForm">Delete</a>';
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['option'])
                ->make(true);
        }

        return view('ticket');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $form = Form::find($id);
        return response()->json($form);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $form = Form::find($id);
        return response()->json($form);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Form::find($id)->delete();
            return redirect()->route('ticketlist.index')->with('success', 'Data deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('ticketlist.index')->with('error', 'Data cannot be deleted');
        }
    }

    public function clearticket()
    {
        DB::table('forms')->truncate();
        return redirect()->route('ticketlist.index')->with('success', 'Data cleared successfully');
    }

    public function exportformexcel()
    {
        return Excel::download(new TicketListExport, 'Tickets_' . date('Y-m-d') . '.xlsx');
    }

    public function exportformexcelperday()
    {
        return Excel::download(new TicketListExportPerDay, 'Tickets_' . date('Y-m-d') . '.xlsx');
    }

    public function importformexcel(Request $request)
    {
        $files = $request->file('files');
        $totalImportedCount = 0;

        foreach ($files as $file) {
            $import = new TicketListImport();
            Excel::import($import, $file);
            $totalImportedCount += $import->importedCount;
        }

        return redirect()->route('ticketlist.index')->with('success', "$totalImportedCount records imported successfully");
    }
}
