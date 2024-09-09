<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Kedb;
use App\Models\User;
use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function index()
    {
        $applications = Application::all()->map(function ($application) {
            $application->app_name = 'IT-HO-Ops-' . $application->app_name;
            return $application;
        });
        $pic_name = User::all();

        $last_form = Form::latest()->first();
        if ($last_form == null) {
            $last_form = (object) [
                'ticket_id' => '',
                'app_id' => '',
                'casename' => '',
                'action' => '',
                'nextaction' => '',
                'evidence' => '',
                'kedb_id' => '',
                'assignment' => '',
                'user_id' => '',
                'starts_at' => '',
                'ends_at' => '',
                'notes' => '',
                'parameter' => '',
                'document' => ''
            ];
        }

        return view('index', compact('applications', 'pic_name', 'last_form'));
    }

    public function form_get_kedb(Request $request)
    {
        $kedb = Kedb::join('applications', 'kedbs.app_id', '=', 'applications.app_id')
            ->select('kedbs.*', 'applications.app_name')
            ->where('kedbs.app_id', $request->app_id)
            ->get();
        return response()->json([
            'status' => 'success',
            'data' => $kedb
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'starts_at' => 'required',
            'ticket_id' => 'required',
            'app_id' => 'required',
            'casename' => 'required|not_regex:/[\r\n]/',
            'action' => 'required|not_regex:/[\r\n]/',
            'nextaction' => 'required|not_regex:/[\r\n]/',
            'evidence' => 'nullable',
            'kedb_id' => 'required',
            'assignment' => 'required',
            'user_id' => 'required',
            'ends_at' => 'required',
            'notes' => 'nullable',
            'parameter' => 'nullable',
            'document' => 'nullable'
        ]);

        $form = new Form();
        $form->ticket_id = $request->ticket_id;
        $form->app_id = $request->app_id;
        $form->casename = $request->casename;
        $form->action = $request->action;
        $form->nextaction = $request->nextaction;
        // $format_evidence = str_replace('"', '""', $request->evidence);
        // $form->evidence = $format_evidence;
        $form->evidence = $request->evidence;
        $form->kedb_id = $request->kedb_id;
        $form->assignment = $request->assignment;
        $form->user_id = $request->user_id;
        $form->starts_at = $request->starts_at;
        $form->ends_at = $request->ends_at;

        $notes_assign_surrounding = "Dear rekan\n\nCase Name : " . $request->casename . "\nAction : " . $request->action . "\nNext Action : " . $request->nextaction . "\nEvidence :\n\n" . $request->evidence . "\n\nTerima kasih";
        $notes_escalate_l2 = "Dear rekan L2\n\nCase Name : " . $request->casename . "\nAction : " . $request->action . "\nNext Action : " . $request->nextaction . "\nEvidence :\n\n" . $request->evidence . "\n\nTerima kasih";
        $notes_resolved = "Dear rekan\n\nCase Name : " . $request->casename . "\nAction : " . $request->action . "\nResolution : " . $request->nextaction . "\nEvidence :\n\n" . $request->evidence . "\n\nTerima kasih";

        if ($request->assignment == 'Assign Surrounding') {
            $form->notes = $notes_assign_surrounding;
        } elseif ($request->assignment == 'Escalate L2') {
            $form->notes = $notes_escalate_l2;
        } elseif ($request->assignment == 'Resolved') {
            $form->notes = $notes_resolved;
        }

        $application = Application::find($request->app_id);
        $kedb = Kedb::find($request->kedb_id);
        $pic = User::find($request->user_id);

        $parameter_assign_surrounding = "~" . $request->ticket_id . "~IT-HO-Ops-" . $application->app_name . "~" . $request->starts_at . "_" . $request->ends_at . "~Case Name : " . $request->casename . "~Action : " . $request->action . "~Next Action / Request Action : " . $request->nextaction . "~KEDB : " . $kedb->kedb_finalisasi;
        $parameter_escalate_l2 = "~" . $request->ticket_id . "~IT-HO-Ops-" . $application->app_name . "~" . $request->starts_at . "_" . $request->ends_at . "~Case Name : " . $request->casename . "~Action : " . $request->action . "~Next Action / Request Action : " . $request->nextaction . "~KEDB : " . $kedb->kedb_finalisasi . "[]L2";
        $parameter_escalate_l2_for_kedbKip = "~" . $request->ticket_id . "~IT-HO-Ops-" . $application->app_name . "~" . $request->starts_at . "_" . $request->ends_at . "~Case Name : " . $request->casename . "~Action : " . $request->action . "~Next Action / Request Action : " . $request->nextaction . "~KEDB : " . $kedb->kedb_finalisasi;
        $parameter_resolved = "~" . $request->ticket_id . "~IT-HO-Ops-" . $application->app_name . "~" . $request->starts_at . "_" . $request->ends_at . "~Case Name : " . $request->casename . "~Action : " . $request->action . "~Resolution : " . $request->nextaction . "~KEDB : " . $kedb->kedb_finalisasi;

        if ($request->assignment == 'Assign Surrounding') {
            $form->parameter = $parameter_assign_surrounding;
        } elseif ($request->assignment == 'Escalate L2') {
            if (strpos($kedb->kedb_finalisasi, 'KIP') !== false) {
                $form->parameter = $parameter_escalate_l2_for_kedbKip;
            } else {
                $form->parameter = $parameter_escalate_l2;
            }
        } elseif ($request->assignment == 'Resolved') {
            $form->parameter = $parameter_resolved;
        }

        $datetime = date('Y/m/d H:i:s', strtotime($request->ends_at));
        $form->notes = str_replace('"', '""', $form->notes);

        $document_assign_surrounding = '"' . $request->ticket_id . '"' . "\t" . '"' . $kedb->kedb_finalisasi . '"' . "\t" . '"' . $request->assignment . '"' . "\t" . '"' . $pic->username . '"' . "\t" . '"' . $form->notes . '"';
        $document_escalate_l2 = '"' . $request->ticket_id . '"' . "\t" . '"' . $kedb->kedb_finalisasi . "[]L2" . '"' . "\t" . '"' . $request->assignment . '"' . "\t" . '"' . $pic->username . '"' . "\t" . '"' . $form->notes . '"';
        $document_escalate_l2_for_kedbKip = '"' . $request->ticket_id . '"' . "\t" . '"' . $kedb->kedb_finalisasi . '"' . "\t" . '"' . $request->assignment . '"' . "\t" . '"' . $pic->username . '"' . "\t" . '"' . $form->notes . '"';
        $document_resolved = '"' . $request->ticket_id . '"' . "\t" . '"' . $kedb->kedb_finalisasi . '"' . "\t" . '"' . $request->assignment . '"' . "\t" . '"' . $pic->username . '"' . "\t" . '"' . $form->notes . '"';

        if ($request->assignment == 'Assign Surrounding') {
            $form->document = $document_assign_surrounding;
        } elseif ($request->assignment == 'Escalate L2') {
            if (strpos($kedb->kedb_finalisasi, 'KIP') !== false) {
                $form->document = $document_escalate_l2_for_kedbKip;
            } else {
                $form->document = $document_escalate_l2;
            }
        } elseif ($request->assignment == 'Resolved') {
            $form->document = $document_resolved;
        }

        // dd($form->notes, $form->parameter, $form->document);
        $form->save();

        return redirect()->route('home')->with('success', 'Form has been submitted');
    }

    public function get_last_form()
    {
        $last_form = Form::latest()->first();
        return response()->json([
            'status' => 'success',
            'data' => $last_form
        ]);
    }

    public function dashboard()
    {
        // Ticket Summary this Year
        $count_ticket = Form::whereDate('created_at', '>=', date('Y'))->count();
        $count_ticket_assign_surrounding = Form::where('assignment', 'Assign Surrounding')->whereDate('created_at', '>=', date('Y'))->count();
        $count_ticket_escalate_l2 = Form::where('assignment', 'Escalate L2')->whereDate('created_at', '>=', date('Y'))->count();
        $count_ticket_resolved = Form::where('assignment', 'Resolved')->whereDate('created_at', '>=', date('Y'))->count();

        $top_kedb_finalisasi = Form::join('kedbs', 'forms.kedb_id', '=', 'kedbs.kedb_id')
            ->select('kedbs.kedb_finalisasi', DB::raw('COUNT(forms.kedb_id) as count'))
            ->groupBy('forms.kedb_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $top_kedb_finalisasi_counts = $top_kedb_finalisasi->pluck('count');
        $top_kedb_finalisasi_labels = $top_kedb_finalisasi->pluck('kedb_finalisasi');

        $ticket_with_most_kedb_id = $top_kedb_finalisasi_labels->first();
        $count_ticket_with_most_kedb_id = $top_kedb_finalisasi_counts->first();

        $top_kedb_finalisasi_total = $top_kedb_finalisasi_counts->sum();

        // Ticket Summary Per Day
        $count_ticket_per_day = Form::whereDate('created_at', date('Y-m-d'))->count();
        $count_ticket_assign_surrounding_per_day = Form::where('assignment', 'Assign Surrounding')->whereDate('created_at', date('Y-m-d'))->count();
        $count_ticket_escalate_l2_per_day = Form::where('assignment', 'Escalate L2')->whereDate('created_at', date('Y-m-d'))->count();
        $count_ticket_resolved_per_day = Form::where('assignment', 'Resolved')->whereDate('created_at', date('Y-m-d'))->count();

        // Ticket Summary H-1
        $count_ticket_h1 = Form::whereDate('created_at', date('Y-m-d', strtotime('-1 day')))->count();
        $count_ticket_assign_surrounding_h1 = Form::where('assignment', 'Assign Surrounding')->whereDate('created_at', date('Y-m-d', strtotime('-1 day')))->count();
        $count_ticket_escalate_l2_h1 = Form::where('assignment', 'Escalate L2')->whereDate('created_at', date('Y-m-d', strtotime('-1 day')))->count();
        $count_ticket_resolved_h1 = Form::where('assignment', 'Resolved')->whereDate('created_at', date('Y-m-d', strtotime('-1 day')))->count();

        // Ticket Summary Per Month
        $count_ticket_per_month = Form::whereMonth('created_at', date('m'))->count();
        $count_ticket_assign_surrounding_per_month = Form::where('assignment', 'Assign Surrounding')->whereMonth('created_at', date('m'))->count();
        $count_ticket_escalate_l2_per_month = Form::where('assignment', 'Escalate L2')->whereMonth('created_at', date('m'))->count();
        $count_ticket_resolved_per_month = Form::where('assignment', 'Resolved')->whereMonth('created_at', date('m'))->count();

        $datenow = Carbon::now()->format('d F Y');
        $month = Carbon::now()->format('F');
        $year = Carbon::now()->format('Y');

        $ticket_chart = DB::table('forms')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Extract labels (dates) and data (counts)
        $labels = $ticket_chart->keys();
        $data = $ticket_chart->values();

        return view('dashboard', compact(
            'count_ticket',
            'count_ticket_assign_surrounding',
            'count_ticket_escalate_l2',
            'count_ticket_resolved',
            'top_kedb_finalisasi_labels',
            'top_kedb_finalisasi_counts',
            'ticket_with_most_kedb_id',
            'count_ticket_with_most_kedb_id',
            'top_kedb_finalisasi_total',
            'count_ticket_per_day',
            'count_ticket_assign_surrounding_per_day',
            'count_ticket_escalate_l2_per_day',
            'count_ticket_resolved_per_day',
            'count_ticket_h1',
            'count_ticket_assign_surrounding_h1',
            'count_ticket_escalate_l2_h1',
            'count_ticket_resolved_h1',
            'count_ticket_per_month',
            'count_ticket_assign_surrounding_per_month',
            'count_ticket_escalate_l2_per_month',
            'count_ticket_resolved_per_month',
            'datenow',
            'labels',
            'data',
            'month'
        ));
    }
}
