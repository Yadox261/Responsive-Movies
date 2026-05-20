<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;

class DailyProjectionsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $schedules;
    public string $dayName;
    public string $recipientName;

    /**
     * Create a new message instance.
     *
     * @param Collection $schedules  Proyecciones del día actual
     * @param string     $dayName    Nombre del día en español (ej: "Martes")
     * @param string     $recipientName Nombre del destinatario
     */
    public function __construct(Collection $schedules, string $dayName, string $recipientName)
    {
        $this->schedules     = $schedules;
        $this->dayName       = $dayName;
        $this->recipientName = $recipientName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $count = $this->schedules->count();
        $label = $count === 1 ? '1 función' : "{$count} funciones";

        return new Envelope(
            subject: "🎬 Cartelera del {$this->dayName}: {$label} programadas — Word of the Movies",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily_projections_report',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
