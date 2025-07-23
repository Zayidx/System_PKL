<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use App\Mail\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PengajuanApprovalController extends Controller
{
    public function approve($token)
    {
        $pengajuan = Pengajuan::where('token', $token)->firstOrFail();
        if (!in_array($pengajuan->status_pengajuan, ['diterima_admin', 'menunggu_konfirmasi_perusahaan'])) {
            abort(403, 'Pengajuan tidak valid atau sudah diproses.');
        }
        $pengajuan->status_pengajuan = 'diterima_perusahaan';
        $pengajuan->save();
        // Generate PDF surat penerimaan
        $pdf = Pdf::loadView('pdf.surat-penerimaan', ['pengajuan' => $pengajuan]);
        // Kirim email ke siswa & admin
        Mail::to($pengajuan->siswa->user->email)->send(new SendEmail('Surat Penerimaan Magang', 'emails.surat-penerimaan', ['pengajuan' => $pengajuan], $pdf->output(), 'Surat-Penerimaan.pdf'));
        return view('emails.approval-success', ['status' => 'diterima', 'pengajuan' => $pengajuan]);
    }
    public function decline($token)
    {
        $pengajuan = Pengajuan::where('token', $token)->firstOrFail();
        if (!in_array($pengajuan->status_pengajuan, ['diterima_admin', 'menunggu_konfirmasi_perusahaan'])) {
            abort(403, 'Pengajuan tidak valid atau sudah diproses.');
        }
        $pengajuan->status_pengajuan = 'ditolak_perusahaan';
        $pengajuan->save();
        // Generate PDF surat penolakan
        $pdf = Pdf::loadView('pdf.surat-penolakan', ['pengajuan' => $pengajuan]);
        // Kirim email ke siswa & admin
        Mail::to($pengajuan->siswa->user->email)->send(new SendEmail('Surat Penolakan Magang', 'emails.surat-penolakan', ['pengajuan' => $pengajuan], $pdf->output(), 'Surat-Penolakan.pdf'));
        return view('emails.approval-success', ['status' => 'ditolak', 'pengajuan' => $pengajuan]);
    }
} 