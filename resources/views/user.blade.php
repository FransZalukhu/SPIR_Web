@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 font-manrope">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Daftar Pengguna</h1>

    <!-- Loading Indicator -->
    <div id="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">Memuat data...</span>
    </div>

    <!-- Error Message -->
    <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <span id="error-text"></span>
    </div>

    <!-- Users Table -->
    <div id="users-table" class="hidden bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Telepon
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody id="users-tbody" class="bg-white divide-y divide-gray-200">
                    <!-- Data akan dimuat di sini -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-12">
        <div class="text-gray-500 text-xl mb-2">Tidak ada data pengguna</div>
        <p class="text-gray-400">Belum ada pengguna yang terdaftar dalam sistem.</p>
    </div>

    <!-- Refresh Button -->
    <div class="mt-6 text-center">
        <button id="refresh-btn"
            class="bg-customGreen hover:bg-customGreenHover text-white font-medium py-2 px-6 rounded-lg transition duration-200 ease-in-out transform hover:scale-105">
            <svg class="inline-block w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
            Refresh Data
        </button>
    </div>

    <!-- View User Modal -->
    <div id="view-user-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex justify-center items-center">
        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
            <!-- Tombol Close -->
            <button id="close-modal-btn" class="absolute top-2 right-3 text-gray-500 hover:text-gray-800 text-xl">
                &times;
            </button>

            <!-- Judul -->
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Detail Pengguna</h2>

            <!-- Konten Detail -->
            <div class="space-y-2 text-gray-700">
                <div><strong>Nama:</strong> <span id="view-name"></span></div>
                <div><strong>No. Telepon:</strong> <span id="view-phone"></span></div>
                <div><strong>Role:</strong> <span id="view-role"></span></div>
                <div><strong>Dibuat:</strong> <span id="view-created-at"></span></div>
            </div>

            <!-- Tombol Tutup -->
            <div class="mt-6 text-right">
                <button id="ok-modal-btn"
                    class="bg-customGreen hover:bg-customGreenHover text-white px-4 py-2 rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const loading = document.getElementById('loading');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const usersTable = document.getElementById('users-table');
    const usersTbody = document.getElementById('users-tbody');
    const emptyState = document.getElementById('empty-state');
    const refreshBtn = document.getElementById('refresh-btn');

    // Load users data
    async function loadUsers() {
        try {
            // Show loading
            showLoading();

            // Fetch data from API
            const response = await fetch('/api/users/secure', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();

            if (result.success) {
                displayUsers(result.data);
            } else {
                throw new Error(result.message || 'Failed to load users');
            }

        } catch (error) {
            console.error('Error loading users:', error);
            showError(`Gagal memuat data pengguna: ${error.message}`);
        }
    }

    // Display users in table
    function displayUsers(users) {
        hideAll();

        if (users.length === 0) {
            emptyState.classList.remove('hidden');
            return;
        }

        // Clear existing data
        usersTbody.innerHTML = '';

        // Add users to table
        users.forEach(user => {
            const row = createUserRow(user);
            usersTbody.appendChild(row);
        });

        usersTable.classList.remove('hidden');
    }

    // Create user row
    function createUserRow(user) {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition-colors duration-200';

        const createdAt = new Date(user.created_at).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const roleClass = user.role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';

        row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${user.id}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">${user.name}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${user.phone_number}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleClass}">
                        ${user.role.charAt(0).toUpperCase() + user.role.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${createdAt}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="bg-blue-600 hover:bg-blue-700 text-white p-2 rounded mr-3" onclick="viewUser(${user.id})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
                <button class="bg-red-600 hover:bg-red-700 text-white p-2 rounded" onclick="deleteUser(${user.id})">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                </td>
            `;
        return row;
    }

    // Show loading state
    function showLoading() {
        hideAll();
        loading.classList.remove('hidden');
    }

    // Show error message
    function showError(message) {
        hideAll();
        errorText.textContent = message;
        errorMessage.classList.remove('hidden');
    }

    // Hide all elements
    function hideAll() {
        loading.classList.add('hidden');
        errorMessage.classList.add('hidden');
        usersTable.classList.add('hidden');
        emptyState.classList.add('hidden');
    }

    // Event listeners
    refreshBtn.addEventListener('click', loadUsers);

    // Initial load
    loadUsers();
});

// Action functions (placeholder - implement as needed)
function viewUser(id) {
    fetch(`/api/users/${id}`)
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                const user = result.data;
                document.getElementById('view-name').textContent = user.name;
                document.getElementById('view-phone').textContent = user.phone_number;
                document.getElementById('view-role').textContent = user.role.charAt(0).toUpperCase() + user.role
                    .slice(1);
                document.getElementById('view-created-at').textContent = new Date(user.created_at).toLocaleString(
                    'id-ID');

                document.getElementById('view-user-modal').classList.remove('hidden');
            } else {
                alert(result.message || 'User tidak ditemukan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat memuat data user.');
        });
}


function deleteUser(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini?')) {
        fetch(`/api/users/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Pengguna berhasil dihapus');
                    loadUsers(); // reload data
                } else {
                    alert(result.message || 'Gagal menghapus pengguna');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan saat menghapus pengguna');
            });
    }
}

document.getElementById('close-modal-btn').addEventListener('click', () => {
    document.getElementById('view-user-modal').classList.add('hidden');
});

document.getElementById('ok-modal-btn').addEventListener('click', () => {
    document.getElementById('view-user-modal').classList.add('hidden');
});
</script>
@endsection