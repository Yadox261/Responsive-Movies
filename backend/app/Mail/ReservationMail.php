<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

<<<<<<< HEAD
/**
 * Clase ReservationMail (Mailable de Laravel)
 * --------------------------------------------
 * Esta clase se encarga de estructurar el correo electrónico enviado al cliente tras
 * una reservación exitosa. Compila el asunto, la plantilla HTML y adjunta al vuelo
 * el boleto PDF generado previamente en memoria.
 */
class ReservationMail extends Mailable
{
    // Queueable habilita el procesamiento asíncrono en colas (background jobs)
    // SerializesModels maneja de forma segura los modelos Eloquent al serializarlos en las colas
    use Queueable, SerializesModels;

    /**
     * Modelo de reservación cargado con datos de base de datos.
     * Al declararse público, Laravel lo hace accesible automáticamente en las vistas de Blade.
     */
    public Reservation $reservation;

    /**
     * Flujo binario del PDF generado por DomPDF en memoria.
     */
    public $pdfContent;

    /**
     * Constructor del Mailable
     * ------------------------
     * Recibe los datos y el PDF en binario para su procesamiento.
     * 
     * @param Reservation $reservation Instancia del modelo guardado en MySQL.
     * @param string $pdfContent Contenido en bytes del boleto PDF.
=======
class ReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public $pdfContent;

    /**
     * Create a new message instance.
>>>>>>> 20d4073506da474cc02fbff7b5d5ca3103efea7b
     */
    public function __construct(Reservation $reservation, $pdfContent)
    {
        $this->reservation = $reservation;
        $this->pdfContent  = $pdfContent;
    }

    /**
<<<<<<< HEAD
     * Definición del Sobre del Correo (Envelope)
     * ------------------------------------------
     * Retorna la configuración básica del envío como el asunto del email,
     * incorporando dinámicamente el nombre de la película reservada.
=======
     * Get the message envelope.
>>>>>>> 20d4073506da474cc02fbff7b5d5ca3103efea7b
     */
    public function envelope(): Envelope
    {
        $movieTitle = $this->reservation->movie->title ?? 'Película';

        return new Envelope(
            subject: "🎟️ Confirmación de Reservación: {$movieTitle} - Word of the Movies",
        );
    }

    /**
<<<<<<< HEAD
     * Definición del Cuerpo/Contenido (Content)
     * ------------------------------------------
     * Indica la ruta de la vista Blade que servirá como plantilla HTML para el
     * correo electrónico ('emails.reservation_confirmation').
=======
     * Get the message content definition.
>>>>>>> 20d4073506da474cc02fbff7b5d5ca3103efea7b
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmation',
        );
    }

    /**
<<<<<<< HEAD
     * Adjuntos del Correo (Attachments)
     * ---------------------------------
     * Utiliza la API 'Attachment::fromData' para inyectar directamente el flujo
     * binario del PDF desde la memoria RAM del servidor como un archivo adjunto descargable,
     * sin necesidad de escribir en el sistema de archivos del servidor (reduciendo la latencia de I/O).
     * 
=======
     * Get the attachments for the message.
     *
>>>>>>> 20d4073506da474cc02fbff7b5d5ca3103efea7b
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
<<<<<<< HEAD
        // Genera un slug seguro y limpio para nombrar el archivo PDF del boleto
=======
>>>>>>> 20d4073506da474cc02fbff7b5d5ca3103efea7b
        $movieSlug = \Illuminate\Support\Str::slug($this->reservation->movie->title ?? 'pelicula');

        return [
            Attachment::fromData(fn () => $this->pdfContent, "Boleto_{$movieSlug}_#{$this->reservation->id}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
