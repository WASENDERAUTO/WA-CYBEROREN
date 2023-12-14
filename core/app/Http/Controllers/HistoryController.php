<?php

namespace App\Http\Controllers;

use App\Helpers\Lyn;
use App\Models\History;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax()){
            if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);
            $auth = auth()->user();
            $table = History::where([
                'user_id' => $auth->id,
                'session_id' => session()->get('main_device'),
            ])->orderBy('created_at', 'desc')->get();

            return datatables()->of($table)
                ->addColumn('responsive_id', function () {
                    return;
                })
                ->editColumn('from', function ($row) {
                    if ($row->from == 'api') {
                        return '<span class="badge bg-label-primary">API</span>';
                    } else if ($row->status == 'single') {
                        return '<span class="badge bg-label-primary">Single Sender</span>';
                    } else if ($row->status == 'responder') {
                        return '<span class="badge bg-label-primary">Responder</span>';
                    }
                })
                ->editColumn('status', function ($row) {
                    if ($row->status == 'sent') {
                        return '<span class="badge bg-label-success">Sent</span>';
                    } else if ($row->status == 'invalid') {
                        return '<span class="badge bg-label-secondary">Invalid</span>';
                    } else if ($row->status == 'failed') {
                        return '<span class="badge bg-label-danger">Failed</span>';
                    }
                })
                ->editColumn('created_at',function($row){
                    return $row->created_at->format('d M Y H:i');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('history.detail', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Show Detail" class="btn btn-icon btn-label-primary me-1"><span class="ti ti-list-details"></span></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        } else {
            if (!session()->get('main_device')) return Lyn::view('nodevice');
            return Lyn::view('history.index');
        }
    }

    public function detail(Request $request, $id){
        if (!session()->get('main_device')) return Lyn::view('nodevice');
        $row = History::where([
            'id' => $id,
            'user_id' => auth()->user()->id,
            'session_id' => session()->get('main_device'),
        ])->first();

        if (!$row) return redirect()->route('history')->withErrors(['message' => 'History not found']);

        $data['row'] = $row;
        $data['data'] = json_decode($row->message);
        return Lyn::view('history.detail', $data);
    }

    public function delete(Request $request)
    {
        if (!$request->ajax()) return response()->json(['message' => 'Bad request'], 400);
        if (!session()->get('main_device')) return response()->json(['message' => 'No main device selected'], 400);

        History::whereIn('id', $request->id)->where([
            'user_id' => auth()->id(),
            'session_id' => session()->get('main_device'),
        ])->delete();

        return response()->json(['message' => 'History deleted.'], 200);
    }
}
