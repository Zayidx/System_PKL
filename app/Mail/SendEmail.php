<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $view;
    public $data;
    public $pdfContent;
    public $pdfName;

    /**
     * Create a new message instance.
     */
    public function __construct($subject = 'Send Email', $view = null, $data = [], $pdfContent = null, $pdfName = null)
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->data = $data;
        $this->pdfContent = $pdfContent;
        $this->pdfName = $pdfName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->view,
            with: $this->data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->pdfContent && $this->pdfName) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromData(fn() => $this->pdfContent, $this->pdfName)
            ];
        }
        return [];
    }
}
