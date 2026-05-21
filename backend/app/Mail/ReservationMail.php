<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation, $pdfContent)
    {
        $this->reservation = $reservation;
        $this->pdfContent  = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $movieTitle = $this->reservation->movie->title ?? 'Película';

        return new Envelope(
            subject: "🎟️ Confirmación de Reservación: {$movieTitle} - Word of the Movies",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $movieSlug = \Illuminate\Support\Str::slug($this->reservation->movie->title ?? 'pelicula');

        return [
            Attachment::fromData(fn () => $this->pdfContent, "Boleto_{$movieSlug}_#{$this->reservation->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
