<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Documento rechazado - Ventanilla Única</title>
</head>

<body
    style="margin:0; padding:0; background-color:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; color:#1a202c;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="background-color:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                    style="max-width:560px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0;">
                    <tr>
                        <td style="padding:32px 32px 12px; text-align:center;">
                            <img src="cid:escudo@salamanca.gob.mx" alt="Escudo de Salamanca" width="96"
                                style="display:inline-block; max-width:120px; height:auto;">
                            <p
                                style="display:block; margin:14px 0 0; color:#334155; font-size:20px; font-weight:700; text-decoration:none; background-color:transparent; border:0;">
                                Ventanilla
                                Única</p>
                            <p style="margin:4px 0 0; color:#64748b; font-size:13px;">Ayuntamiento de Salamanca</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            <div
                                style="display:inline-block; background-color:#fdeceb; color:#ef4444; font-weight:700; font-size:13px; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                Documento rechazado
                            </div>
                            <p style="font-size:15px; line-height:1.6; margin:0 0 12px;">Hola {{ $nombreUsuario }},
                            </p>
                            <p style="font-size:15px; line-height:1.6; margin:0 0 12px;">
                                El documento <strong>{{ $nombreDocumento }}</strong> ha sido
                                <strong>rechazado</strong>.
                            </p>
                            @if ($motivoRechazo)
                                <div
                                    style="border-left:4px solid #374151; margin:0 0 20px; background-color:#f8fafc; padding:16px;">
                                    <p style="margin:0 0 4px; font-size:13px; font-weight:bold; color:#1a202c;">Motivo
                                        del
                                        rechazo:</p>
                                    <p style="margin:0; font-size:15px; line-height:1.5;">{{ $motivoRechazo }}</p>
                                </div>
                            @endif
                            <p style="font-size:15px; line-height:1.6; margin:0 0 20px;">
                                Para realizar las correcciones necesarias, ingresa al portal e inicia sesión en tu
                                cuenta.
                            </p>
                            <div style="text-align:center;">
                                <a href="{{ $urlPortal }}"
                                    style="display:inline-block; background-color:#374151; color:#ffffff; text-decoration:none; font-weight:600; font-size:14px; padding:12px 24px; border-radius:8px;">
                                    Ingresar al portal
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="padding:20px 32px; background-color:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#64748b;">Este es un correo automático, por favor
                                no lo respondas.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
