@extends('layouts.app')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
    rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<h1 class="text-3xl font-bold mb-6">Welcome to Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Jumlah User</h2>
        <p class="text-2xl font-bold">{{ \App\Models\User::count() }}</p>
    </div>
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold">Jumlah Laporan</h2>
        <p class="text-2xl font-bold">{{ \App\Models\Report::count() }}</p>
    </div>
</div>

<div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Bar Chart: Jumlah User -->
    <div class="bg-white p-6 rounded shadow chart-container">
        <h2 class="text-lg font-semibold mb-2">Jumlah User per Bulan</h2>
        <canvas id="userChart" class="max-h-80 h-80 w-full"></canvas>
    </div>

    <!-- Donut Chart: Status Laporan -->
    <div class="bg-white p-6 rounded shadow chart-container">
        <h2 class="text-lg font-semibold mb-2">Distribusi Laporan</h2>
        <canvas id="donutChart" class="max-h-80 h-80 w-full"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const response = await fetch("{{ route('admin.dashboard.data') }}");
        const data = await response.json();

        const months = data.months;
        const userCounts = data.userCounts;
        const statusCounts = Object.values(data.statusCounts);

        // Chart: User per bulan
        const ctxBar = document.getElementById('userChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Jumlah User Terdaftar',
                    data: userCounts,
                    backgroundColor: '#3b82f6'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Donut: Status laporan
        const ctxDonut = document.getElementById('donutChart').getContext('2d');
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Diverifikasi', 'Diproses', 'Selesai'],
                datasets: [{
                    data: statusCounts,
                    backgroundColor: ['#facc15', '#3b82f6', '#6366f1', '#22c55e'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });
    });
</script>
@endsection