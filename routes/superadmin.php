    <?php
use App\Livewire\Admin\UserDashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\UserDashboard as AdminUserManagement; // Alias agar lebih jelas

       // --- GRUP UNTUK SUPERADMIN ---
    // Diberi prefix 'admin', nama 'admin.', dan dilindungi oleh middleware 'role:superadmin'
    Route::prefix('admin')->name('admin.')->middleware('role:superadmin')->group(function () {
        
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');

        // Grup untuk data master (contoh: manajemen user oleh admin)
        Route::prefix('master-user')->name('master-user.*')->group(function () {
            Route::get('/users', UserDashboard::class)->name('master-users');
            // Tambahkan rute master data lainnya di sini...
            // Route::get('/products', AdminProductManagement::class)->name('products');
        });
    });
