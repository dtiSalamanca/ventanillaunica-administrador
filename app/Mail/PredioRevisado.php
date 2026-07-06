<?php

namespace App\Mail;

use App\Models\Predio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PredioRevisado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Predio $predio)
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
                'urlPerfil' => rtrim((string) config('services.ventanilla_ciudadano.base_url'), '/').'/perfiles/mi-perfil',
            ],
        );
    }
}
