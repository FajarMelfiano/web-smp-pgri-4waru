<?php

namespace App\Mail;

use App\Models\Pendaftar;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PendaftarBaruMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Pendaftar $pendaftar) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pendaftar Baru PPDB: '.$this->pendaftar->nama_siswa,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: sprintf(
                '<p>Pendaftar baru tercatat di situs SMP PGRI 4 Waru:</p><ul><li>Siswa: %s</li><li>Asal SD: %s</li><li>Wali: %s</li><li>WhatsApp: %s</li><li>Catatan: %s</li></ul>',
                e($this->pendaftar->nama_siswa),
                e($this->pendaftar->asal_sd),
                e($this->pendaftar->nama_wali),
                e($this->pendaftar->whatsapp),
                e($this->pendaftar->catatan ?? '-')
            ),
        );
    }
}
