<?php

namespace App\Http\Controllers;

use App\Models\Sop;
use Illuminate\Http\Request;

class SopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Sop::when($request->term, function ($query, $term) {
                $query->where('sop_name', 'LIKE', '%' . $term . '%')
                    ->orWhere('sop_link', 'LIKE', '%' . $term . '%');
            })->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('option', function ($sop) {
                    $button = '<div class="d-flex">';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $sop->sop_id . '" data-original-title="Edit" class="btn btn-primary btn-sm editSop">Edit</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $sop->sop_id . '" data-original-title="Link" class="btn btn-primary btn-sm linkSop">Link</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $sop->sop_id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteSop">Delete</a>';
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['option'])
                ->make(true);
        }

        return view('sop');
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
        Sop::updateOrCreate(
            ['sop_id' => $request->sop_id],
            ['sop_name' => $request->sop_name, 'sop_link' => $request->sop_link]
        );

        return response()->json(['success' => 'SOP saved successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sop = Sop::find($id);
        return response()->json($sop);
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
            Sop::find($id)->delete();
            return redirect()->route('sop.index')->with('success', 'SOP deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sop.index')->with('error', 'SOP cannot be deleted.');
        }
    }
}
