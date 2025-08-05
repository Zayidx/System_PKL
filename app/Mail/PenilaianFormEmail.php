<?php

namespace App\Mail;

use App\Models\Prakerin;
use App\Models\Siswa;
use App\Models\Perusahaan;
use App\Models\PembimbingPerusahaan;
use App\Models\Kompetensi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PenilaianFormEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $prakerin;
    public $siswa;
    public $perusahaan;
    public $pembimbingPerusahaan;
    public $kompetensi;
    public $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Prakerin $prakerin, Siswa $siswa, Perusahaan $perusahaan, PembimbingPerusahaan $pembimbingPerusahaan, $kompetensi, $token)
    {
        $this->prakerin = $prakerin;
        $this->siswa = $siswa;
        $this->perusahaan = $perusahaan;
        $this->pembimbingPerusahaan = $pembimbingPerusahaan;
        $this->kompetensi = $kompetensi;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Form Penilaian PKL - ' . $this->siswa->nama_siswa,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.penilaian-form',
            with: [
                'prakerin' => $this->prakerin,
                'siswa' => $this->siswa,
                'perusahaan' => $this->perusahaan,
                'pembimbingPerusahaan' => $this->pembimbingPerusahaan,
                'kompetensi' => $this->kompetensi,
                'token' => $this->token,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
} 