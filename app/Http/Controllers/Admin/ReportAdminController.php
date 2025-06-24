<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportAdminController extends Controller
{

    public function showReportPage()
    {
        return view('report');
    }

    public function index(Request $request)
    {
        $query = Report::with(['user', 'category'])->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $limit = $request->get('limit', 10);
        $reports = $query->paginate($limit);

        return response()->json([
            'status' => true,
            'data' => $reports,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,diverifikasi,diproses,selesai',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $report = Report::find($id);

        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => 'Report not found.',
            ], 404);
        }

        $report->status = $request->status;
        $report->save();

        return response()->json([
            'status' => true,
            'message' => 'Report status updated successfully.',
            'data' => $report,
        ]);
    }

    public function getChartData()
    {
        $statuses = ['pending', 'diverifikasi', 'diproses', 'selesai'];
        $data = [];

        foreach ($statuses as $status) {
            $count = Report::where('status', $status)->count();
            $data[$status] = $count;
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
}
