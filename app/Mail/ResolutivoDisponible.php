<?php

namespace App\Mail;

use App\Mail\Concerns\EmbedsEscudo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class ResolutivoDisponible extends Mailable
{
    use EmbedsEscudo, Queueable, SerializesModels;

    public function build(): void
    {
        $this->withSymfonyMessage(fn (Email $message) => $this->incrustarEscudo($message));
    }

    /**
     * @param  string  $nombreUsuario  Nombre del ciudadano destinatario.
     * @param  string  $nombreTramite  Nombre del trámite concluido.
     * @param  string  $urlPortal  Enlace al portal ciudadano para consultar el resolutivo.
     */
    public function __construct(
        public string $nombreUsuario,
        public string $nombreTramite,
        public string $urlPortal,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu trámite ha concluido - Ventanilla Única',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.solicitudes.resolutivo',
            with: [
                'nombreUsuario' => $this->nombreUsuario,
                'nombreTramite' => $this->nombreTramite,
                'urlPortal' => $this->urlPortal,
            ],
        );
    }
}
