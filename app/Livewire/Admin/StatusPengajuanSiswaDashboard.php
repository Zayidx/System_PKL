<?php

namespace App\Livewire\Admin;

use App\Models\Pengajuan;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Siswa;

#[Layout('components.layouts.layout-staff-hubin-dashboard')]
#[Title('Status Pengajuan Siswa')]
class StatusPengajuanSiswaDashboard extends Component
{
      protected $paginationTheme = 'bootstrap';
    use WithPagination;

    public $nis;
    public $siswa;
    public $search = '';
    public $perPage = 10;
    public $sortBy = 'id_pengajuan';
    public $sortDir = 'desc';

    protected $listeners = [
        'approvePengajuan' => 'approvePengajuan',
        'declinePengajuan' => 'declinePengajuan',
    ];

    public function mount($nis)
    {
        $this->nis = $nis;
        $this->siswa = Siswa::with('kelas')->findOrFail($nis);
    }

    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = ($this->sortDir === 'asc') ? 'desc' : 'asc';
            return;
        }
        $this->sortBy = $column;
        $this->sortDir = 'asc';
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function approvePengajuan($id)
    {
        $pengajuan = Pengajuan::with(['siswa.user', 'perusahaan'])->findOrFail($id);
        if ($pengajuan->status_pengajuan !== 'pending') {
            $this->dispatch('swal:error', ['message' => 'Pengajuan ini sudah diproses sebelumnya!']);
            return;
        }
        $pengajuan->status_pengajuan = 'diterima_admin';
        $pengajuan->token = Str::random(32);
        $pengajuan->save();
        
        $pdf = Pdf::loadView('pdf.surat-pengajuan', ['pengajuan' => $pengajuan]);
        $approveUrl = route('pengajuan.approve', $pengajuan->token);
        $declineUrl = route('pengajuan.decline', $pengajuan->token);

        Mail::to($pengajuan->perusahaan->email_perusahaan)->send(
            new SendEmail(
                'Pengajuan Pemagangan Siswa',
                'emails.surat-pengajuan',
                [
                    'pengajuan' => $pengajuan,
                    'approveUrl' => $approveUrl,
                    'declineUrl' => $declineUrl,
                ],
                $pdf->output(),
                'Surat-Pengajuan.pdf'
            )
        );
        $this->dispatch('swal:success', ['message' => 'Pengajuan berhasil disetujui dan email telah dikirim ke perusahaan!']);
    }

    public function declinePengajuan($id)
    {
        $pengajuan = Pengajuan::with(['siswa.user', 'perusahaan'])->findOrFail($id);
        if ($pengajuan->status_pengajuan !== 'pending') {
            $this->dispatch('swal:error', ['message' => 'Pengajuan ini sudah diproses sebelumnya!']);
            return;
        }
        $pengajuan->status_pengajuan = 'ditolak_admin';
        $pengajuan->save();

        $pdf = Pdf::loadView('pdf.surat-penolakan', ['pengajuan' => $pengajuan]);
        
        Mail::to($pengajuan->siswa->user->email)->send(
            new SendEmail(
                'Pengajuan Magang Ditolak',
                'emails.surat-penolakan',
                ['pengajuan' => $pengajuan],
                $pdf->output(),
                'Surat-Penolakan.pdf'
            )
        );
        $this->dispatch('swal:success', ['message' => 'Pengajuan ditolak dan email telah dikirim ke siswa!']);
    }

    public function render()
    {
        $pengajuanTerdaftar = \App\Models\Pengajuan::with(['perusahaan'])
            ->where('nis_siswa', $this->nis)
            ->where('is_perusahaan_terdaftar', true)
            ->whereHas('perusahaan', function ($query) {
                $query->where('nama_perusahaan', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage, ['*'], 'terdaftar');

        $pengajuanTidakTerdaftar = \App\Models\Pengajuan::where('nis_siswa', $this->nis)
            ->where('is_perusahaan_terdaftar', false)
            ->where(function ($query) {
                $query->where('nama_perusahaan_manual', 'like', '%' . $this->search . '%')
                      ->orWhere('alamat_perusahaan_manual', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage, ['*'], 'tidak_terdaftar');

        return view('livewire.admin.status-pengajuan-siswa-dashboard', [
            'pengajuanTerdaftar' => $pengajuanTerdaftar,
            'pengajuanTidakTerdaftar' => $pengajuanTidakTerdaftar,
        ]);
    }
}
