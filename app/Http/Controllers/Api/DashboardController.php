<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login')->with('error', 'Anda harus Login untuk mengakses Dashboard!.');
        }

        return view('dashboard');
    }

    public function getData()
    {
        $months = [];
        $userCounts = [];

        $now = Carbon::now();
        for ($i = 11; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $monthName = $date->format('M Y');
            $count = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $months[] = $monthName;
            $userCounts[] = $count;
        }

        $statusCounts = [
            'pending' => Report::where('status', 'pending')->count(),
            'diverifikasi' => Report::where('status', 'diverifikasi')->count(),
            'diproses' => Report::where('status', 'diproses')->count(),
            'selesai' => Report::where('status', 'selesai')->count(),
        ];

        return response()->json([
            'months' => $months,
            'userCounts' => $userCounts,
            'statusCounts' => $statusCounts,
        ]);
    }
}
