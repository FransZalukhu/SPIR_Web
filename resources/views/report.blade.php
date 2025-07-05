@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container mx-auto px-4 py-6 font-manrope">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Daftar Laporan</h1>

    <!-- Filter Status -->
    <div class="mb-4">
        <label for="status" class="block text-sm font-medium text-gray-700">Filter Status</label>
        <select id="status-filter" class="mt-1 block w-60 rounded-md border-gray-300 shadow-sm focus:border-customGreen focus:ring-customGreen">
            <option value="">Semua</option>
            <option value="pending">Pending</option>
            <option value="diverifikasi">Diverifikasi</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
        </select>
    </div>

    <!-- Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-6 py-3 text-left">Pelapor</th>
                    <th class="px-6 py-3 text-left">Isi Laporan</th>
                    <th class="px-6 py-3 text-left">Kategori</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Tanggal</th>
                    <th class="px-6 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody id="report-body" class="divide-y divide-gray-200 text-sm">
                <!-- Rows via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail -->
<div id="detail-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Detail Laporan</h2>
        <div id="detail-content" class="text-sm text-gray-700 space-y-4">
            <!-- Konten detail akan diisi oleh JavaScript -->
        </div>
        <div class="text-right mt-6">
            <button onclick="closeModal('detail-modal')"
                class="px-4 py-2 bg-customGreen hover:bg-customGreenHover text-white rounded">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal Update -->
<div id="update-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4 text-gray-800">Update Status</h2>
        <form id="update-form">
            <input type="hidden" id="update-id">
            <label for="update-status" class="block text-sm font-medium text-gray-700">Pilih Status</label>
            <select id="update-status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-customGreen focus:ring-customGreen">
                <option value="pending">Pending</option>
                <option value="diverifikasi">Diverifikasi</option>
                <option value="diproses">Diproses</option>
                <option value="selesai">Selesai</option>
            </select>
            <div class="text-right mt-4 space-x-2">
                <button type="button" onclick="closeModal('update-modal')"
                    class="px-4 py-2 bg-gray-400 hover:bg-gray-500 text-white rounded">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-customGreen hover:bg-customGreenHover text-white rounded">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportBody = document.getElementById('report-body');
        const statusFilter = document.getElementById('status-filter');

        async function loadReports(status = '') {
            const url = status ? `/admin/reports?status=${status}` : '/admin/reports';
            const response = await fetch(url, {
                method: 'GET',
                credentials: 'include'
            });
            const result = await response.json();
            if (result.status) renderReports(result.data.data);
        }

        function renderReports(reports) {
            reportBody.innerHTML = '';
            if (reports.length === 0) {
                reportBody.innerHTML =
                    `<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada laporan ditemukan.</td></tr>`;
                return;
            }

            reports.forEach(report => {
                const row = document.createElement('tr');
                const statusBadge = getStatusBadge(report.status);
                row.innerHTML = `
                    <td class="px-6 py-4">${report.user?.name ?? '-'}</td>
                    <td class="px-6 py-4">${report.description ?? '-'}</td>
                    <td class="px-6 py-3 text-left">${report.category?.name ?? '-'}</td>
                    <td class="px-6 py-4">${statusBadge}</td>
                    <td class="px-6 py-4">${new Date(report.created_at).toLocaleDateString('id-ID')}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded mr-3" onclick='showDetail(${JSON.stringify(report)})' title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                        <button class="bg-green-600 hover:bg-green-700 text-white p-2 rounded mr-3" onclick='showUpdateModal(${report.id})' title="Update Status">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                    </td>
                `;
                reportBody.appendChild(row);
            });
        }

        function getStatusBadge(status) {
            const colorMap = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'diverifikasi': 'bg-blue-100 text-blue-800',
                'diproses': 'bg-indigo-100 text-indigo-800',
                'selesai': 'bg-green-100 text-green-800',
            };
            const color = colorMap[status] || 'bg-gray-100 text-gray-800';
            const statusCapitalized = status.charAt(0).toUpperCase() + status.slice(1);
            return `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${color}">${statusCapitalized}</span>`;
        }

        window.showDetail = function(report) {
            const detailContent = document.getElementById('detail-content');
            detailContent.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Pelapor:</strong> ${report.user?.name ?? '-'}</p>
                        <p><strong>Judul:</strong> ${report.title ?? '-'}</p>
                        <p><strong>Deskripsi:</strong> ${report.description ?? '-'}</p>
                        <p><strong>Kategori:</strong> ${report.category?.name ?? '-'}</p>
                        <p><strong>Status:</strong> ${report.status.charAt(0).toUpperCase() + report.status.slice(1)}</p>
                        <p><strong>Lokasi:</strong> ${report.location ?? '-'}</p>
                        <p><strong>Koordinat:</strong> ${report.latitude}, ${report.longitude}</p>
                        <p><strong>Tanggal Dibuat:</strong> ${new Date(report.created_at).toLocaleString('id-ID')}</p>
                    </div>
                    ${report.photo_url ? `
                        <div>
                            <strong>Foto:</strong><br>
                            <img src="${report.photo_url}" alt="Foto Laporan" class="mt-2 rounded shadow max-w-full h-auto"/>
                        </div>
                    ` : ''}
                </div>
            `;
            openModal('detail-modal');
        }

        window.showUpdateModal = function(id) {
            document.getElementById('update-id').value = id;
            openModal('update-modal');
        }

        document.getElementById('update-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('update-id').value;
            const status = document.getElementById('update-status').value;

            const response = await fetch(`/admin/reports/${id}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status
                }),
                credentials: 'include'
            });

            const result = await response.json();
            if (result.status) {
                closeModal('update-modal');
                loadReports(statusFilter.value);
            } else {
                alert('Gagal memperbarui status: ' + (result.message || 'Kesalahan tidak diketahui.'));
            }
        });

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        window.closeModal = closeModal;
        window.openModal = openModal;

        // Event filter
        statusFilter.addEventListener('change', function() {
            loadReports(this.value);
        });

        // Load awal
        loadReports();
    });
</script>
@endsection