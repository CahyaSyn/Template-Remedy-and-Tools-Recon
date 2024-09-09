<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Application::when($request->term, function ($query, $term) {
                $query->where('app_name', 'LIKE', '%' . $term . '%');
            })->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('option', function ($app) {
                    $button = '<div class="d-flex">';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $app->app_id . '" data-original-title="Edit" class="btn btn-primary btn-sm editApp">Edit</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $app->app_id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteApp">Delete</a>';
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['option'])
                ->make(true);
        }

        $app = Application::all();
        return view('application', compact('app'));
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
        try {
            Application::updateOrCreate(['app_id' => $request->app_id], $request->all());
            return response()->json(['success' => 'Application saved successfully.']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Application cannot be saved.']);
        }
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
        $app = Application::find($id);
        return response()->json($app);
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
        // $request->validate([
        //     'app_name' => 'required',
        // ]);

        // $request['app_name'] = strtoupper($request['app_name']);

        // Application::find($id)->update($request->all());

        // return redirect()->route('application.index')
        //     ->with('success', 'Application updated successfully.');
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
            Application::find($id)->delete();
            return redirect()->route('application.index')->with('success', 'Application deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('application.index')->with('error', 'Application cannot be deleted.');
        }
    }
}
