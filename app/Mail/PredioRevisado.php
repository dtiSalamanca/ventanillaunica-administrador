<?php

namespace App\Mail;

use App\Mail\Concerns\EmbedsEscudo;
use App\Models\Predio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class PredioRevisado extends Mailable
{
    use EmbedsEscudo, Queueable, SerializesModels;

    public function build(): void
    {
        $this->withSymfonyMessage(fn (Email $message) => $this->incrustarEscudo($message));
    }

    public function __construct(public Predio $predio, public ?string $motivoRechazo = null)
    {
        //
    }

    public function envelope(): Envelope
    {
        $aprobado = $this->predio->estatus_predio === Predio::ESTATUS_APROBADO;

        return new Envelope(
            subject: $aprobado
                ? "Tu predio {$this->predio->clave_predio} fue aprobado"
                : "Tu predio {$this->predio->clave_predio} fue rechazado",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.predios.revisado',
            with: [
                'aprobado' => $this->predio->estatus_predio === Predio::ESTATUS_APROBADO,
                'nombreUsuario' => $this->predio->usuario->name,
                'clavePredio' => $this->predio->clave_predio,
                'motivoRechazo' => $this->motivoRechazo,
                'urlPerfil' => $this->urlCiudadanoPerfil(),
            ],
        );
    }

    /**
     * URL del perfil del ciudadano garantizando el esquema http(s).
     */
    private function urlCiudadanoPerfil(): string
    {
        $base = rtrim((string) config('services.ventanilla_ciudadano.base_url'), '/');

        if (! preg_match('~^https?://~i', $base)) {
            $base = 'http://'.$base;
        }

        return $base.'/perfiles/mi-perfil';
    }
}
