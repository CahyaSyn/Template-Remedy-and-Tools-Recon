<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::when($request->term, function ($query, $term) {
                $query->where('username', 'LIKE', '%' . $term . '%')
                    ->orWhere('ldap', 'LIKE', '%' . $term . '%')
                    ->orWhere('email_tsel', 'LIKE', '%' . $term . '%')
                    ->orWhere('email_solusi', 'LIKE', '%' . $term . '%')
                    ->orWhere('email_gmail', 'LIKE', '%' . $term . '%')
                    ->orWhere('no_hp', 'LIKE', '%' . $term . '%')
                    ->orWhere('no_wa', 'LIKE', '%' . $term . '%')
                    ->orWhere('role', 'LIKE', '%' . $term . '%')
                    ->orWhere('office_site', 'LIKE', '%' . $term . '%')
                    ->orWhere('hire_date', 'LIKE', '%' . $term . '%');
            })->get();
            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('option', function ($pic) {
                    $button = '<div class="d-flex">';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $pic->user_id . '" data-original-title="Edit" class="btn btn-primary btn-sm editPic">Edit</a>';
                    $button .= '&nbsp;&nbsp;';
                    $button .= '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $pic->user_id . '" data-original-title="Delete" class="btn btn-danger btn-sm deletePic">Delete</a>';
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['option'])
                ->make(true);
        }
        $pic = User::all();
        return view('pic', compact('pic'));
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
            User::updateOrCreate(['user_id' => $request->user_id], [
                'ldap' => $request->ldap,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email_tsel' => $request->email_tsel,
                'email_solusi' => $request->email_solusi,
                'email_gmail' => $request->email_gmail,
                'no_hp' => $request->no_hp,
                'no_wa' => $request->no_wa,
                'role' => $request->role,
                'office_site' => $request->office_site,
                'hire_date' => $request->hire_date
            ]);
            return response()->json(['success' => 'PIC has been saved successfully']);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Save PIC failed']);
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
        $pic = User::find($id);
        return response()->json($pic);
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
        //     User::find($id)->update($request->all());
        //     return response()->json(['success' => 'PIC has been updated successfully']);
        // } catch (\Throwable $th) {
        //     return response()->json(['error' => 'Update PIC failed']);
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
            User::find($id)->delete();
            return redirect()->route('pic.index')->with('success', 'PIC deleted successfully');
        } catch (\Throwable $th) {
            return redirect()->route('pic.index')->with('error', 'PIC cannot be deleted');
        }
    }

    public function importusercsv(Request $request)
    {
        $file = $request->file('file_user_csv');
        try {
            $csvData = file_get_contents($file);
        } catch (\Throwable $th) {
            return redirect()->route('pic.index')
                ->with('error', 'File not found.');
        }

        $lines = explode(PHP_EOL, $csvData);
        $array = array();
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }

        $count_user_exist = 0;
        $count_user_not_exist = 0;
        foreach ($array as $key => $value) {
            $temp = explode("|", $value[0]);

            if (count($temp) != 11) {
                return redirect()->route('pic.index')
                    ->with('error', 'Invalid CSV format. Please check the CSV file.');
            }

            $user = User::where('ldap', $temp[1])->first();

            if ($user) {
                $user->update([
                    'ldap' => $temp[1],
                    'username' => $temp[0],
                    'password' => Hash::make($temp[2]),
                    'email_tsel' => $temp[3],
                    'email_solusi' => $temp[6],
                    'email_gmail' => $temp[7],
                    'no_hp' => $temp[4],
                    'no_wa' => $temp[5],
                    'role' => $temp[8],
                    'office_site' => $temp[9],
                    'hire_date' => $temp[10]
                ]);
                $count_user_exist++;
            } else {
                User::create([
                    'ldap' => $temp[1],
                    'username' => $temp[0],
                    'password' => Hash::make($temp[2]),
                    'email_tsel' => $temp[3],
                    'email_solusi' => $temp[6],
                    'email_gmail' => $temp[7],
                    'no_hp' => $temp[4],
                    'no_wa' => $temp[5],
                    'role' => $temp[8],
                    'office_site' => $temp[9],
                    'hire_date' => $temp[10]
                ]);
                $count_user_not_exist++;
            }
        }

        return response()->json([
            'success' => 'PIC has been imported successfully',
            'count_user_exist' => $count_user_exist,
            'count_user_not_exist' => $count_user_not_exist
        ]);
    }
}
