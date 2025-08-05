<?php

namespace App\Livewire\Admin;

use App\Models\Kompetensi;
use App\Models\Jurusan;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout("components.layouts.layout-admin-dashboard")]
class KompetensiNilaiDashboard extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $editingKompetensi = null;
    public $nama_kompetensi = '';
    public $id_jurusan = '';
    public $confirmingDelete = false;
    public $kompetensiToDelete = null;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'nama_kompetensi' => 'required|string|max:255',
        'id_jurusan' => 'required|exists:jurusan,id_jurusan'
    ];

    protected $messages = [
        'nama_kompetensi.required' => 'Nama kompetensi wajib diisi.',
        'nama_kompetensi.max' => 'Nama kompetensi maksimal 255 karakter.',
        'id_jurusan.required' => 'Jurusan wajib dipilih.',
        'id_jurusan.exists' => 'Jurusan yang dipilih tidak valid.'
    ];

    public function mount()
    {
        // Cek apakah user adalah superadmin
        if (!auth()->check() || auth()->user()->role->name !== 'superadmin') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->editingKompetensi = null;
        $this->resetForm();
        $this->dispatch('showModal');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingKompetensi = null;
        $this->resetForm();
        $this->dispatch('hideModal');
    }

    public function resetForm()
    {
        $this->nama_kompetensi = '';
        $this->id_jurusan = '';
        $this->resetValidation();
    }

    public function editKompetensi($id)
    {
        $kompetensi = Kompetensi::findOrFail($id);
        $this->editingKompetensi = $kompetensi;
        $this->nama_kompetensi = $kompetensi->nama_kompetensi;
        $this->id_jurusan = $kompetensi->id_jurusan;
        $this->showModal = true;
    }

    public function saveKompetensi()
    {
        $this->validate();

        try {
            if ($this->editingKompetensi) {
                // Update existing kompetensi
                $this->editingKompetensi->update([
                    'nama_kompetensi' => $this->nama_kompetensi,
                    'id_jurusan' => $this->id_jurusan
                ]);

                Log::info('Kompetensi berhasil diupdate', [
                    'kompetensi_id' => $this->editingKompetensi->id_kompetensi,
                    'nama_kompetensi' => $this->nama_kompetensi,
                    'id_jurusan' => $this->id_jurusan,
                    'user_id' => auth()->id()
                ]);

                $this->dispatch('swal:success', [
                    'message' => 'Kompetensi berhasil diperbarui!'
                ]);
            } else {
                // Create new kompetensi
                Kompetensi::create([
                    'nama_kompetensi' => $this->nama_kompetensi,
                    'id_jurusan' => $this->id_jurusan
                ]);

                Log::info('Kompetensi berhasil dibuat', [
                    'nama_kompetensi' => $this->nama_kompetensi,
                    'id_jurusan' => $this->id_jurusan,
                    'user_id' => auth()->id()
                ]);

                $this->dispatch('swal:success', [
                    'message' => 'Kompetensi berhasil ditambahkan!'
                ]);
            }

            $this->closeModal();
            $this->dispatch('kompetensiUpdated');

        } catch (\Exception $e) {
            Log::error('Error menyimpan kompetensi', [
                'error' => $e->getMessage(),
                'data' => [
                    'nama_kompetensi' => $this->nama_kompetensi,
                    'id_jurusan' => $this->id_jurusan
                ]
            ]);

            $this->dispatch('swal:error', [
                'message' => 'Terjadi kesalahan saat menyimpan kompetensi. Silakan coba lagi.'
            ]);
        }
    }

    public function confirmDelete($id)
    {
        $this->kompetensiToDelete = Kompetensi::findOrFail($id);
        $this->confirmingDelete = true;
        $this->dispatch('showDeleteModal');
    }

    public function deleteKompetensi()
    {
        try {
            $kompetensiName = $this->kompetensiToDelete->nama_kompetensi;
            $kompetensiId = $this->kompetensiToDelete->id_kompetensi;

            $this->kompetensiToDelete->delete();

            Log::info('Kompetensi berhasil dihapus', [
                'kompetensi_id' => $kompetensiId,
                'nama_kompetensi' => $kompetensiName,
                'user_id' => auth()->id()
            ]);

            $this->dispatch('swal:success', [
                'message' => "Kompetensi '{$kompetensiName}' berhasil dihapus!"
            ]);

        } catch (\Exception $e) {
            Log::error('Error menghapus kompetensi', [
                'error' => $e->getMessage(),
                'kompetensi_id' => $this->kompetensiToDelete->id_kompetensi ?? null
            ]);

            $this->dispatch('swal:error', [
                'message' => 'Terjadi kesalahan saat menghapus kompetensi. Silakan coba lagi.'
            ]);
        }

        $this->confirmingDelete = false;
        $this->kompetensiToDelete = null;
        $this->dispatch('hideDeleteModal');
        $this->dispatch('kompetensiUpdated');
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->kompetensiToDelete = null;
        $this->dispatch('hideDeleteModal');
    }

    public function render()
    {
        $query = Kompetensi::with('jurusan');

        if ($this->search) {
            $query->where('nama_kompetensi', 'like', '%' . $this->search . '%')
                  ->orWhereHas('jurusan', function($q) {
                      $q->where('nama_jurusan_lengkap', 'like', '%' . $this->search . '%')
                        ->orWhere('nama_jurusan_singkat', 'like', '%' . $this->search . '%');
                  });
        }

        $kompetensi = $query->orderBy('id_jurusan')
                           ->orderBy('nama_kompetensi')
                           ->paginate(10);

        $jurusan = Jurusan::orderBy('nama_jurusan_lengkap')->get();

        return view('livewire.admin.kompetensi-nilai-dashboard', [
            'kompetensi' => $kompetensi,
            'jurusan' => $jurusan
        ]);
    }
} 