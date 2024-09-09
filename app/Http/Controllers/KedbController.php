<?php

namespace App\Http\Controllers;

use App\Models\Kedb;
use App\Models\KedbChild;
use App\Models\KedbParent;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KedbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $term = $request->term;
            $kedbs = Kedb::join('applications', 'kedbs.app_id', '=', 'applications.app_id')
                ->leftJoin('kedb_parents', 'kedbs.kedb_parent_id', '=', 'kedb_parents.kedb_parent_id')
                ->leftJoin('kedb_children', 'kedbs.kedb_child_id', '=', 'kedb_children.kedb_child_id')
                ->select('kedbs.*', 'applications.app_name', 'kedb_parents.kedb_parent_name', 'kedb_children.kedb_child_name')
                ->when($term, function ($query) use ($term) {
                    $query->where(function ($query) use ($term) {
                        $query->where('kedb_finalisasi', 'LIKE', '%' . $term . '%')
                            ->orWhere('app_name', 'LIKE', '%' . $term . '%')
                            ->orWhere('kedb_parent_name', 'LIKE', '%' . $term . '%')
                            ->orWhere('kedb_child_name', 'LIKE', '%' . $term . '%');
                    });
                })
                ->get();

            return datatables()->of($kedbs)
                ->addindexColumn()
                ->addColumn('option', function ($row) {
                    $button = '<div class="d-flex">';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->kedb_id . '" data-original-title="Edit" class="btn btn-primary btn-sm editKedb">Edit</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->kedb_id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteKedb">Delete</a>';
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['option'])
                ->make(true);
        }
        return view('kedb');
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
        // update or create
        $kedb = Kedb::updateOrCreate(
            ['kedb_id' => $request->kedb_id],
            [
                'kedb_parent_id' => $request->kedb_parent_id,
                'kedb_child_id' => $request->kedb_child_id,
                'app_id' => $request->app_id,
                'old_kedb' => $request->old_kedb,
                'new_symtom_kedb' => $request->new_symtom_kedb,
                'new_specific_symtom_kedb' => $request->new_specific_symtom_kedb,
                'kedb_finalisasi' => $request->kedb_finalisasi,
                'action' => $request->action,
                'responsibility_action' => $request->responsibility_action,
                'sop' => $request->sop
            ]
        );
        if ($kedb->wasRecentlyCreated) {
            return response()->json([
                'success' => 'Kedb created successfully'
            ]);
        } else {
            return response()->json([
                'success' => 'Kedb updated successfully'
            ]);
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
        $kedb = Kedb::join('applications', 'kedbs.app_id', '=', 'applications.app_id')
            ->leftJoin('kedb_parents', 'kedbs.kedb_parent_id', '=', 'kedb_parents.kedb_parent_id')
            ->leftJoin('kedb_children', 'kedbs.kedb_child_id', '=', 'kedb_children.kedb_child_id')
            ->select('kedbs.*', 'applications.app_name', 'kedb_parents.kedb_parent_name', 'kedb_children.kedb_child_name')
            ->where('kedb_id', $id)
            ->first();
        return response()->json($kedb);
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
        // try {
        //     Kedb::find($id)->update($request->all());
        //     return response()->json([
        //         'success' => 'Kedb updated successfully'
        //     ]);
        // } catch (\Throwable $th) {
        //     return response()->json([
        //         'error' => 'Kedb update failed'
        //     ]);
        // }
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
            Kedb::find($id)->delete();
            return redirect()->route('kedb.index')
                ->with('success', 'Kedb deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->route('kedb.index')
                ->with('error', 'Kedb delete failed');
        }
    }

    public function clearKedb()
    {
        try {
            Kedb::truncate();
            return redirect()->route('kedb.index')
                ->with('success', 'Kedb cleared successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('kedb.index')
                ->with('error', 'Kedb cannot be cleared.');
        }
    }

    public function importkedbkipcsv(Request $request)
    {
        $file = $request->file('file_kedb_kip');
        try {
            $csvData = file_get_contents($file);
        } catch (\Throwable $th) {
            return redirect()->route('kedb.index')
                ->with('error', 'File not found.');
        }

        $lines = explode(PHP_EOL, $csvData);
        $array = array();
        foreach ($lines as $key => $value) {
            $lines[$key] = str_replace(",", "/", $value);
        }
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }

        $count_kedb_exist = 0;
        $count_kedb_not_exist = 0;
        foreach ($array as $key => $value) {
            $temp = explode("|", $value[0]);
            if ($temp[2] === '99%USAHAKU') {
                $temp[2] = '99%Usahaku';
            } elseif ($temp[2] === 'B2BMYENTERPRISE') {
                $temp[2] = 'B2BMyEnterprise';
            } elseif ($temp[2] === 'CUG') {
                $temp[2] = 'CUGCorporate';
            } elseif ($temp[2] === 'MEA') {
                $temp[2] = 'MyEnterpriseAccess';
            } elseif ($temp[2] === 'TOPUPCORPORATE') {
                $temp[2] = 'TopUpCorporate';
            } elseif ($temp[2] === 'TOPUPMYENTERPRISE') {
                $temp[2] = 'TopUpMyEnterprise';
            } elseif ($temp[2] === 'IKNOW') {
                $temp[2] = 'iKnow';
            }
            if (count($temp) != 10) {
                return redirect()->route('kedb.index')
                    ->with('error', 'File format is not correct.');
            }
            foreach ($temp as $key => $value) {
                if ($value === "") {
                    $temp[$key] = null;
                }
            }

            $app = Application::where('app_name', $temp[2])->first();
            if ($app === null) {
                Application::create([
                    'app_name' => $temp[2]
                ]);
            }

            $kedb_parent = KedbParent::where('kedb_parent_name', $temp[0])->first();
            if ($kedb_parent === null) {
                KedbParent::create([
                    'kedb_parent_name' => $temp[0]
                ]);
            }

            $kedb_child = KedbChild::where('kedb_child_name', $temp[1])->first();
            if ($kedb_child === null) {
                KedbChild::create([
                    'kedb_parent_id' => DB::table('kedb_parents')->where('kedb_parent_name', $temp[0])->first()->kedb_parent_id,
                    'kedb_child_name' => $temp[1]
                ]);
            }

            $kedb = Kedb::where('kedb_finalisasi', $temp[6])->first();

            if ($kedb) {
                $kedb->update([
                    'kedb_parent_id' => DB::table('kedb_parents')->where('kedb_parent_name', $temp[0])->first()->kedb_parent_id,
                    'kedb_child_id' => DB::table('kedb_children')->where('kedb_child_name', $temp[1])->first()->kedb_child_id,
                    'app_id' => DB::table('applications')->where('app_name', $temp[2])->first()->app_id,
                    'old_kedb' => $temp[3],
                    'new_symtom_kedb' => $temp[4],
                    'new_specific_symtom_kedb' => $temp[5],
                    'kedb_finalisasi' => $temp[6],
                    'action' => $temp[7],
                    'responsibility_action' => $temp[8],
                    'sop' => $temp[9]
                ]);
                $count_kedb_exist++;
            } else {
                Kedb::create([
                    'kedb_parent_id' => DB::table('kedb_parents')->where('kedb_parent_name', $temp[0])->first()->kedb_parent_id,
                    'kedb_child_id' => DB::table('kedb_children')->where('kedb_child_name', $temp[1])->first()->kedb_child_id,
                    'app_id' => DB::table('applications')->where('app_name', $temp[2])->first()->app_id,
                    'old_kedb' => $temp[3],
                    'new_symtom_kedb' => $temp[4],
                    'new_specific_symtom_kedb' => $temp[5],
                    'kedb_finalisasi' => $temp[6],
                    'action' => $temp[7],
                    'responsibility_action' => $temp[8],
                    'sop' => $temp[9]
                ]);
                $count_kedb_not_exist++;
            }
        }

        return response()->json([
            'success' => 'Kedb has been imported successfully',
            'count_kedb_exist' => $count_kedb_exist,
            'count_kedb_not_exist' => $count_kedb_not_exist
        ]);
    }

    public function importoldkedbcsv(Request $request)
    {
        $file = $request->file('file_kedb');
        try {
            $csvData = file_get_contents($file);
        } catch (\Throwable $th) {
            return redirect()->route('kedb.index')
                ->with('error', 'File not found.');
        }

        $lines = explode(PHP_EOL, $csvData);
        $array = array();
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }

        $count_oldkedb_exist = 0;
        $kedb_name_exist = array();
        $count_oldkedb_not_exist = 0;
        foreach ($array as $key => $value) {
            $temp = explode("[]", $value[0]);
            if ($temp[1] === '99%USAHAKU') {
                $temp[1] = '99%Usahaku';
            } elseif ($temp[1] === 'B2BMYENTERPRISE') {
                $temp[1] = 'B2BMyEnterprise';
            } elseif ($temp[1] === 'CUG') {
                $temp[1] = 'CUGCorporate';
            } elseif ($temp[1] === 'MEA') {
                $temp[1] = 'MyEnterpriseAccess';
            } elseif ($temp[1] === 'TOPUPCORPORATE') {
                $temp[1] = 'TopUpCorporate';
            } elseif ($temp[1] === 'TOPUPMYENTERPRISE') {
                $temp[1] = 'TopUpMyEnterprise';
            } elseif ($temp[1] === 'IKNOW') {
                $temp[1] = 'iKnow';
            }
            if (count($temp) != 5) {
                return redirect()->route('kedb.index')
                    ->with('error', 'File format is not correct.');
            }

            $app = Application::where('app_name', $temp[1])->first();
            if ($app === null) {
                Application::create([
                    'app_name' => $temp[1]
                ]);
            }

            $kedb = Kedb::where('kedb_finalisasi', $value[0])->first();

            if ($kedb) {
                $kedb->update([
                    'app_id' => DB::table('applications')->where('app_name', $temp[1])->first()->app_id,
                    'kedb_finalisasi' => $value[0]
                ]);
                $kedb_name_exist[] = $value[0];
                $count_oldkedb_exist++;
            } else {
                Kedb::create([
                    'app_id' => DB::table('applications')->where('app_name', $temp[1])->first()->app_id,
                    'kedb_finalisasi' => $value[0]
                ]);
                $count_oldkedb_not_exist++;
            }
        }

        return response()->json([
            'success' => 'Kedb has been imported successfully',
            'count_oldkedb_exist' => $count_oldkedb_exist,
            'count_oldkedb_not_exist' => $count_oldkedb_not_exist,
            'kedb_name_exist' => $kedb_name_exist
        ]);
    }

    public function exportcsv()
    {
        $kedbs = Kedb::join('applications', 'kedbs.app_id', '=', 'applications.app_id')
            ->leftJoin('kedb_parents', 'kedbs.kedb_parent_id', '=', 'kedb_parents.kedb_parent_id')
            ->leftJoin('kedb_children', 'kedbs.kedb_child_id', '=', 'kedb_children.kedb_child_id')
            ->select('kedbs.*', 'applications.app_name', 'kedb_parents.kedb_parent_name', 'kedb_children.kedb_child_name')
            ->get();
        $filename = "kedb.csv";
        $handle = fopen($filename, 'w+');
        fputcsv($handle, array('kedb_parent_name', 'kedb_child_name', 'app_name', 'old_kedb', 'new_symtom_kedb', 'new_specific_symtom_kedb', 'kedb_finalisasi', 'action', 'responsibility_action', 'sop'));
        foreach ($kedbs as $row) {
            fputcsv($handle, array($row->kedb_parent_name, $row->kedb_child_name, $row->app_name, $row->old_kedb, $row->new_symtom_kedb, $row->new_specific_symtom_kedb, $row->kedb_finalisasi, $row->action, $row->responsibility_action, $row->sop));
        }
        fclose($handle);
        $headers = array(
            'Content-Type' => 'text/csv',
        );
        return response()->download($filename, 'kedb.csv', $headers);
    }

    public function getapp(Request $request)
    {
        $app = Application::all();
        return response()->json($app);
    }

    public function getparent(Request $request)
    {
        $parent = KedbParent::all();
        return response()->json($parent);
    }

    public function getchild(Request $request)
    {
        $child = KedbChild::all();
        return response()->json($child);
    }

    public function getkedb(Request $request)
    {
        $kedb = Kedb::join('applications', 'kedbs.app_id', '=', 'applications.app_id')
            ->leftJoin('kedb_parents', 'kedbs.kedb_parent_id', '=', 'kedb_parents.kedb_parent_id')
            ->leftJoin('kedb_children', 'kedbs.kedb_child_id', '=', 'kedb_children.kedb_child_id')
            ->select('kedbs.*', 'applications.app_name', 'kedb_parents.kedb_parent_name', 'kedb_children.kedb_child_name')
            ->get();
        return response()->json($kedb);
    }
}
