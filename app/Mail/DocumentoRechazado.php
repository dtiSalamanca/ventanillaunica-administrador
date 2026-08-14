<?php

namespace App\Mail;

use App\Mail\Concerns\EmbedsEscudo;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class DocumentoRechazado extends Mailable
{
    use EmbedsEscudo, Queueable, SerializesModels;

    public function build(): void
    {
        $this->withSymfonyMessage(fn (Email $message) => $this->incrustarEscudo($message));
    }

    /**
     * @param  string  $nombreUsuario  Nombre del ciudadano destinatario.
     * @param  string  $nombreDocumento  Nombre del documento rechazado.
     * @param  string  $motivoRechazo  Razón por la que se rechazó el documento.
     * @param  string  $urlPortal  Enlace al portal ciudadano para corregir el documento.
     */
    public function __construct(
        public string $nombreUsuario,
        public string $nombreDocumento,
        public string $motivoRechazo,
        public string $urlPortal,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Documento rechazado - Ventanilla Única',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.documentos.rechazado',
            with: [
                'nombreUsuario' => $this->nombreUsuario,
                'nombreDocumento' => $this->nombreDocumento,
                'motivoRechazo' => $this->motivoRechazo,
                'urlPortal' => $this->urlPortal,
            ],
        );
    }
}
