<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i>
                        Manajemen Kompetensi PKL
                    </h5>
                    <div>
                        <a href="{{ route('admin.penilaian-pkl') }}" class="btn btn-info me-2">
                            <i class="bi bi-clipboard-data me-2"></i>
                            Lihat Penilaian PKL
                        </a>
                        <button class="btn btn-primary" wire:click="openModal">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Kompetensi
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       placeholder="Cari kompetensi atau jurusan..."
                                       wire:model.live="search">
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">
                                Total: {{ $kompetensi->total() }} kompetensi
                            </span>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kompetensi</th>
                                    <th>Jurusan</th>
                                    <th>Jumlah Penilaian</th>
                                    <th>Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kompetensi as $index => $item)
                                    <tr>
                                        <td>{{ $kompetensi->firstItem() + $index }}</td>
                                        <td>
                                            <strong>{{ $item->nama_kompetensi }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $item->jurusan->nama_jurusan_singkat }}
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $item->jurusan->nama_jurusan_lengkap }}
                                            </small>
                                        </td>
                                        <td>
                                            @php
                                                $jumlahPenilaian = \App\Models\Nilai::where('id_kompetensi', $item->id_kompetensi)->count();
                                            @endphp
                                            <span class="badge bg-{{ $jumlahPenilaian > 0 ? 'success' : 'secondary' }}">
                                                {{ $jumlahPenilaian }} penilaian
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $item->created_at ? $item->created_at->format('d M Y H:i') : 'N/A' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        wire:click="editKompetensi({{ $item->id_kompetensi }})"
                                                        title="Edit Kompetensi">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        wire:click="confirmDelete({{ $item->id_kompetensi }})"
                                                        title="Hapus Kompetensi"
                                                        @if($jumlahPenilaian > 0) disabled @endif>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox display-4"></i>
                                                <p class="mt-2">Tidak ada data kompetensi</p>
                                                @if($search)
                                                    <small>Coba ubah kata kunci pencarian</small>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $kompetensi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah/Edit Kompetensi -->
    <div class="modal fade" id="kompetensiModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-{{ $editingKompetensi ? 'pencil' : 'plus-circle' }} me-2"></i>
                        {{ $editingKompetensi ? 'Edit Kompetensi' : 'Tambah Kompetensi Baru' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                
                <form wire:submit.prevent="saveKompetensi">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_kompetensi" class="form-label">
                                Nama Kompetensi <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_kompetensi') is-invalid @enderror" 
                                   id="nama_kompetensi"
                                   wire:model="nama_kompetensi"
                                   placeholder="Masukkan nama kompetensi">
                            @error('nama_kompetensi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="id_jurusan" class="form-label">
                                Jurusan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('id_jurusan') is-invalid @enderror" 
                                    id="id_jurusan"
                                    wire:model="id_jurusan">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusan as $j)
                                    <option value="{{ $j->id_jurusan }}">
                                        {{ $j->nama_jurusan_lengkap }} ({{ $j->nama_jurusan_singkat }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_jurusan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($editingKompetensi)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Info:</strong> Mengubah kompetensi ini akan mempengaruhi semua penilaian yang menggunakan kompetensi ini.
                            </div>
                        @endif
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            <i class="bi bi-x-circle me-2"></i>
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveKompetensi">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ $editingKompetensi ? 'Update' : 'Simpan' }}
                            </span>
                            <span wire:loading wire:target="saveKompetensi">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Konfirmasi Hapus
                    </h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                
                <div class="modal-body">
                    @if($kompetensiToDelete)
                        <p>Apakah Anda yakin ingin menghapus kompetensi:</p>
                        <div class="alert alert-warning">
                            <strong>{{ $kompetensiToDelete->nama_kompetensi }}</strong>
                            <br>
                            <small class="text-muted">
                                Jurusan: {{ $kompetensiToDelete->jurusan->nama_jurusan_lengkap }}
                            </small>
                        </div>
                        
                        @php
                            $jumlahPenilaian = \App\Models\Nilai::where('id_kompetensi', $kompetensiToDelete->id_kompetensi)->count();
                        @endphp
                        
                        @if($jumlahPenilaian > 0)
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Peringatan:</strong> Kompetensi ini memiliki {{ $jumlahPenilaian }} penilaian. 
                                Menghapus kompetensi ini akan mempengaruhi data penilaian yang ada.
                            </div>
                        @endif
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">
                        <i class="bi bi-x-circle me-2"></i>
                        Batal
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deleteKompetensi" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteKompetensi">
                            <i class="bi bi-trash me-2"></i>
                            Hapus
                        </span>
                        <span wire:loading wire:target="deleteKompetensi">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Menghapus...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('livewire:init', () => {
    // Handle modal show/hide
    Livewire.on('kompetensiUpdated', () => {
        // Refresh data if needed
    });

    // Show modal when showModal is true
    Livewire.on('showModal', () => {
        const modal = new bootstrap.Modal(document.getElementById('kompetensiModal'));
        modal.show();
    });

    // Hide modal when showModal is false
    Livewire.on('hideModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('kompetensiModal'));
        if (modal) {
            modal.hide();
        }
    });

    // Show delete confirmation modal
    Livewire.on('showDeleteModal', () => {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    });

    // Hide delete confirmation modal
    Livewire.on('hideDeleteModal', () => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (modal) {
            modal.hide();
        }
    });
});

// Watch for showModal changes
Livewire.on('$refresh', () => {
    if (@this.showModal) {
        const modal = new bootstrap.Modal(document.getElementById('kompetensiModal'));
        modal.show();
    } else {
        const modal = bootstrap.Modal.getInstance(document.getElementById('kompetensiModal'));
        if (modal) {
            modal.hide();
        }
    }

    if (@this.confirmingDelete) {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    } else {
        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
        if (modal) {
            modal.hide();
        }
    }
});

// Handle modal events
document.addEventListener('DOMContentLoaded', function() {
    // Handle kompetensi modal
    const kompetensiModal = document.getElementById('kompetensiModal');
    if (kompetensiModal) {
        kompetensiModal.addEventListener('hidden.bs.modal', function () {
            @this.closeModal();
        });
    }

    // Handle delete modal
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('hidden.bs.modal', function () {
            @this.cancelDelete();
        });
    }
});
</script> 