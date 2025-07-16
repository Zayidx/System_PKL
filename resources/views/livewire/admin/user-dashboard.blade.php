<div>
    {{-- ========================================================================================= --}}
    {{-- FILE VIEW LIVEWIRE: Tampilan Komponen --}}
    {{-- Path: resources/views/livewire/admin/user-dashboard.blade.php --}}
    {{-- ========================================================================================= --}}

    {{-- Card untuk menampung tabel manajemen user --}}
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Manajemen User (Livewire)</h4>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                {{-- Tombol ini memanggil method 'create' di komponen Livewire --}}
                <button class="btn btn-primary" wire:click="create">Tambah User Baru</button>
            </div>
    
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="user-table">
                    <thead>
                        <tr>
                            <th>No</th><th>Foto</th><th>Username</th><th>Email</th><th>Role</th><th width="150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $index => $user)
                            <tr wire:key="{{ $user->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td><img src="{{ $user->foto ? Storage::url($user->foto) : 'https://placehold.co/100x100/EEE/31343C?text=No+Image' }}" alt="{{ $user->username }}" class="table-img"></td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-info">{{ $user->role->name ?? 'N/A' }}</span></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" wire:click="edit({{ $user->id }})">Edit</button>
                                    <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $user->id }})">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Tidak ada data user.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL LIVEWIRE -->
    @if ($isModalOpen)
    <div class="modal fade show" style="display: block;" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $userId ? 'Edit User' : 'Tambah User Baru' }}</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Form ini memanggil method 'store' saat disubmit --}}
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" wire:model.defer="username">
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model.defer="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model.defer="password" placeholder="{{ $userId ? 'Kosongkan jika tidak diubah' : '' }}">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control @error('foto') is-invalid @enderror" id="foto" wire:model="foto">
                            @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            
                            <div wire:loading wire:target="foto" class="text-muted mt-1">Mengunggah...</div>

                            {{-- Preview untuk foto baru atau foto yang sudah ada --}}
                            @if ($foto)
                                <img src="{{ $foto->temporaryUrl() }}" class="preview-img mt-2">
                            @elseif ($existingFoto)
                                <img src="{{ $existingFoto }}" class="preview-img mt-2">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="roles_id" class="form-label">Role</label>
                            <select class="form-select @error('roles_id') is-invalid @enderror" id="roles_id" wire:model.defer="roles_id">
                                <option value="">Pilih Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('roles_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="modal-footer pb-0">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="store">Simpan</span>
                                <span wire:loading wire:target="store">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
