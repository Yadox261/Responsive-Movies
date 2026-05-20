<?php

namespace App\Mail;

use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MovieScheduleMail extends Mailable
{
    use Queueable, SerializesModels;

    public Schedule $schedule;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(Schedule $schedule, $pdfContent)
    {
        $this->schedule   = $schedule;
        $this->pdfContent = $pdfContent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $movieTitle = $this->schedule->movie->title ?? 'Película';

        return new Envelope(
            subject: "🎬 Nuevo Horario Confirmado: {$movieTitle} - Word of the Movies",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.movie_schedule',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $movieSlug = \Illuminate\Support\Str::slug($this->schedule->movie->title ?? 'pelicula');

        return [
            Attachment::fromData(fn () => $this->pdfContent, "Horario_{$movieSlug}_#{$this->schedule->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
