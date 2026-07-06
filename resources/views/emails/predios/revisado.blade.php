<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $aprobado ? 'Predio aprobado' : 'Predio rechazado' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; color:#1a202c;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e2e8f0;">
                    <tr>
                        <td style="background-color:#601028; padding:28px 32px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:20px; font-weight:700;">Ventanilla Única</h1>
                            <p style="margin:6px 0 0; color:rgba(255,255,255,0.9); font-size:13px;">Ayuntamiento de Salamanca</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:32px;">
                            @if ($aprobado)
                                <div style="display:inline-block; background-color:#e6f9f1; color:#10b981; font-weight:700; font-size:13px; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                    Predio aprobado
                                </div>
                                <p style="font-size:15px; line-height:1.6; margin:0 0 12px;">Hola {{ $nombreUsuario }},</p>
                                <p style="font-size:15px; line-height:1.6; margin:0 0 12px;">
                                    Tu predio con clave catastral <strong>{{ $clavePredio }}</strong> ha sido <strong>aprobado</strong>.
                                </p>
                                <p style="font-size:15px; line-height:1.6; margin:0;">
                                    No necesitas realizar ninguna acción adicional por el momento.
                                </p>
                            @else
                                <div style="display:inline-block; background-color:#fdeceb; color:#ef4444; font-weight:700; font-size:13px; padding:6px 14px; border-radius:999px; margin-bottom:16px;">
                                    Predio rechazado
                                </div>
                                <p style="font-size:15px; line-height:1.6; margin:0 0 12px;">Hola {{ $nombreUsuario }},</p>
                                <p style="font-size:15px; line-height:1.6; margin:0 0 12px;">
                                    Tu predio con clave catastral <strong>{{ $clavePredio }}</strong> ha sido <strong>rechazado</strong>.
                                </p>
                                <p style="font-size:15px; line-height:1.6; margin:0 0 20px;">
                                    Ingresa a tu perfil para corregir la clave catastral y enviarlo nuevamente a revisión.
                                </p>
                                <div style="text-align:center;">
                                    <a href="{{ $urlPerfil }}" style="display:inline-block; background-color:#601028; color:#ffffff; text-decoration:none; font-weight:600; font-size:14px; padding:12px 24px; border-radius:8px;">
                                        Ir a mi perfil
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px; background-color:#f8fafc; border-top:1px solid #e2e8f0; text-align:center;">
                            <p style="margin:0; font-size:12px; color:#64748b;">Este es un correo automático, por favor no lo respondas.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
