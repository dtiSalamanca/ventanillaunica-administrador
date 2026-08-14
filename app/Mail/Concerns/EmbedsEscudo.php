<?php

namespace App\Mail\Concerns;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

trait EmbedsEscudo
{
    /**
     * Incrusta el escudo de Salamanca como imagen inline (CID) en el correo,
     * de modo que se muestre junto al contenido sin que Gmail lo trate como
     * archivo adjunto.
     *
     * El Content-ID coincide con el usado en el portal ciudadano
     * (ventanillaunica-ciudadano) y con la referencia `cid:escudo@salamanca.gob.mx`
     * empleada en las vistas de correo.
     */
    protected function incrustarEscudo(Email $message): void
    {
        $ruta = public_path('images/escudoArma.png');

        if (! is_file($ruta)) {
            return;
        }

        // El contenido se pasa como string y el nombre como null para que el
        // DataPart no defina "filename"; setName('') evita el "name=" en el MIME.
        $parte = new DataPart((string) file_get_contents($ruta), null, 'image/png');
        $parte->setContentId('escudo@salamanca.gob.mx');
        $parte->setName('');

        $message->addPart($parte);
    }
}
