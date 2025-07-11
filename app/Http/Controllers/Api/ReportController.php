<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5012',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'public');
        }

        $report = Report::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => true,
            'data' => $report->makeHidden(['photo_path'])->toArray() + [
                'photo_url' => $report->photo_url,
                'category' => $report->category ? $report->category->name : null
            ],
        ]);
    }

    public function destroyOwnReport($id, Request $request)
    {
        $user = $request->user();

        $report = Report::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => 'Laporan tidak ditemukan atau Anda tidak memiliki akses.',
            ], 404);
        }

        if ($report->photo_path && Storage::disk('public')->exists($report->photo_path)) {
            Storage::disk('public')->delete($report->photo_path);
        }

        $report->delete();

        return response()->json([
            'status' => true,
            'message' => 'Laporan berhasil dihapus.',
        ]);
    }

    public function myReports(Request $request)
    {
        $user = $request->user();

        $reports = Report::with(['category'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return $report->makeHidden(['photo_path'])->toArray() + [
                    'photo_url' => $report->photo_url,
                    'category' => $report->category?->name,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $reports,
        ]);
    }

    public function index()
    {
        $reports = Report::with(['user', 'category', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($report) {
                return $report->makeHidden(['photo_path'])->toArray() + [
                    'photo_url' => $report->photo_url,
                    'category' => $report->category?->name,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $reports,
        ]);
    }

    public function show($id)
    {
        $report = Report::with(['user', 'category'])->find($id);

        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => 'Report not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $report->makeHidden(['photo_path'])->toArray() + [
                'photo_url' => $report->photo_url,
                'category' => $report->category?->name,
            ],
        ]);
    }
}
