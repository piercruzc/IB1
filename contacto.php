<?php
/**
 * Script de procesamiento de formulario - IBM FINTECH S.A.C.
 * Envío de emails mediante PHPMailer y SMTP
 */

// Incluir PHPMailer (instalado vía Composer)
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Configuración de errores (desactivar display en producción)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Configuración del charset
header('Content-Type: text/html; charset=utf-8');

// ===== CONFIGURACIÓN SMTP =====
define('SMTP_HOST', 'smtp.gmail.com');        // Ejemplo: smtp.gmail.com, smtp.hostinger.com
define('SMTP_PORT', 587);                           // 587 para TLS, 465 para SSL
define('SMTP_USER', 'team.mkt@investmentbm.com');    // Tu usuario SMTP
define('SMTP_PASS', 'jpcvqtqeuukpdvjr');          // Tu contraseña SMTP
define('SMTP_SECURE', PHPMailer::ENCRYPTION_STARTTLS); // TLS o PHPMailer::ENCRYPTION_SMTPS para SSL

// ===== CONFIGURACIÓN DE EMAIL =====
//$email_destino = "informacion@investmenbm.com";
$email_destino = "taek.korn@gmail.com";
$email_copia = ""; // Email opcional para copia (CC)
$nombre_empresa = "IBM FINTECH S.A.C.";
$email_remitente = SMTP_USER;

// ===== VALIDACIÓN Y SANITIZACIÓN DE DATOS =====
function limpiar_dato($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validar_telefono($telefono) {
    return preg_match('/^[\d\s\-\(\)\+]+$/', $telefono);
}

// ===== VERIFICAR QUE SEA UNA PETICIÓN POST =====
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.html");
    exit();
}

// ===== RECIBIR Y VALIDAR DATOS DEL FORMULARIO =====
session_start();
$errores = array();

// Nombre completo (requerido)
if (empty($_POST["nombre"])) {
    $errores[] = "El nombre es requerido";
} else {
    $nombre = limpiar_dato($_POST["nombre"]);
    if (strlen($nombre) < 3) {
        $errores[] = "El nombre debe tener al menos 3 caracteres";
    }
}

// Teléfono (requerido)
if (empty($_POST["telefono"])) {
    $errores[] = "El teléfono es requerido";
} else {
    $telefono = limpiar_dato($_POST["telefono"]);
    if (!validar_telefono($telefono)) {
        $errores[] = "El formato del teléfono no es válido";
    }
}

// Email (requerido)
if (empty($_POST["email"])) {
    $errores[] = "El email es requerido";
} else {
    $email = limpiar_dato($_POST["email"]);
    if (!validar_email($email)) {
        $errores[] = "El formato del email no es válido";
    }
}

// Motivo de consulta (requerido)
if (empty($_POST["consulta"])) {
    $errores[] = "El motivo de consulta es requerido";
} else {
    $consulta = limpiar_dato($_POST["consulta"]);
}

// Mensaje (opcional pero recomendado)
$mensaje = !empty($_POST["mensaje"]) ? limpiar_dato($_POST["mensaje"]) : "Sin mensaje adicional";

// Política de privacidad (requerido)
if (!isset($_POST["privacidad"]) || $_POST["privacidad"] != "on") {
    $errores[] = "Debe aceptar la política de privacidad";
}

// ===== SI HAY ERRORES, REDIRIGIR =====
if (count($errores) > 0) {
    $_SESSION['errores'] = $errores;
    header("Location: index.html#contacto");
    exit();
}

// ===== MAPEO DE OPCIONES DE CONSULTA =====
$opciones_consulta = array(
    "asesoria-finanzas-personales" => "Asesoría en Finanzas Personales",
    "estructuracion-y-reestructuracion-de-deuda" => "Estructuración y Reestructuración de Deuda",
    "asesoria-inversion-ahorro" => "Asesoría en Inversión y Ahorro",
    "gestion-patrimonio" => "Gestión de Patrimonio",
    "curso-finanzas-personales-inteligentes" => "Curso: Finanzas Personales Inteligentes",
    "curso-inversiones-activos-digitales" => "Curso: Inversiones en Activos Digitales",
    "curso-inversiones-bolsa-de-valores" => "Curso: Inversiones en Bolsa de Valores",
    "nivel-1" => "Plan Nivel 1 (S/ 500 - S/ 10,000)",
    "nivel-2" => "Plan Nivel 2 (S/ 10,001 - S/ 20,000)",
    "nivel-3" => "Plan Nivel 3 (S/ 20,001 a más)"
);

$consulta_texto = isset($opciones_consulta[$consulta]) ? $opciones_consulta[$consulta] : $consulta;

// ===== PREPARAR EL EMAIL =====
$asunto = "Nueva solicitud de asesoría - IBM FINTECH";

// Crear el cuerpo del email en HTML
$cuerpo_html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Solicitud de Asesoría</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #22499a 0%, #165ea9 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">
                                IBM FINTECH S.A.C.
                            </h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 14px;">
                                Nueva Solicitud de Asesoría
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Contenido -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #22499a; margin: 0 0 20px 0; font-size: 20px;">
                                Datos del Cliente
                            </h2>
                            
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <strong style="color: #333333; display: inline-block; width: 150px;">Nombre:</strong>
                                        <span style="color: #666666;">' . htmlspecialchars($nombre) . '</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <strong style="color: #333333; display: inline-block; width: 150px;">Email:</strong>
                                        <span style="color: #666666;">' . htmlspecialchars($email) . '</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <strong style="color: #333333; display: inline-block; width: 150px;">Teléfono:</strong>
                                        <span style="color: #666666;">' . htmlspecialchars($telefono) . '</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e5e7eb;">
                                        <strong style="color: #333333; display: inline-block; width: 150px;">Motivo:</strong>
                                        <span style="color: #22499a; font-weight: bold;">' . htmlspecialchars($consulta_texto) . '</span>
                                    </td>
                                </tr>
                            </table>
                            
                            <h3 style="color: #22499a; margin: 30px 0 15px 0; font-size: 18px;">
                                Mensaje del Cliente
                            </h3>
                            <div style="background-color: #f8fafc; padding: 20px; border-radius: 8px; border-left: 4px solid #22499a;">
                                <p style="color: #333333; margin: 0; line-height: 1.6; font-size: 14px;">
                                    ' . nl2br(htmlspecialchars($mensaje)) . '
                                </p>
                            </div>
                            
                            <div style="margin-top: 30px; padding: 20px; background-color: #f0f9ff; border-radius: 8px; text-align: center;">
                                <p style="color: #22499a; margin: 0; font-size: 14px;">
                                    <strong>📧 Responder a:</strong> ' . htmlspecialchars($email) . '<br>
                                    <strong>📱 WhatsApp:</strong> ' . htmlspecialchars($telefono) . '
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 20px 30px; text-align: center; border-top: 1px solid #e5e7eb;">
                            <p style="color: #666666; margin: 0; font-size: 12px;">
                                Fecha: ' . date('d/m/Y H:i:s') . '<br>
                            </p>
                            <p style="color: #999999; margin: 10px 0 0 0; font-size: 11px;">
                                Este email fue generado automáticamente desde el formulario de contacto de IBM FINTECH S.A.C.
                            </p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
';

// Crear versión de texto plano como alternativa
$cuerpo_texto = "
NUEVA SOLICITUD DE ASESORÍA - IBM FINTECH S.A.C.
================================================

DATOS DEL CLIENTE:
------------------
Nombre: $nombre
Email: $email
Teléfono: $telefono
Motivo: $consulta_texto

MENSAJE:
--------
$mensaje

------------------
Fecha: " . date('d/m/Y H:i:s') . "

Este email fue generado automáticamente desde el formulario de contacto.
";

// ===== ENVIAR EMAIL PRINCIPAL CON PHPMAILER =====
$mail = new PHPMailer(true);
$enviado = false;

try {
    // Configuración del servidor SMTP
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = SMTP_SECURE;
    $mail->Port       = SMTP_PORT;

    // Desactivar debug en producción
    $mail->SMTPDebug  = 0; // 0 = off, 1 = client, 2 = client y server

    // Configuración del email
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom($email_remitente, $nombre_empresa);
    $mail->addAddress($email_destino);

    // Agregar copia si está configurada
    if (!empty($email_copia)) {
        $mail->addCC($email_copia);
    }

    $mail->addReplyTo($email, $nombre);

    // Contenido del email
    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $mail->Body    = $cuerpo_html;
    $mail->AltBody = $cuerpo_texto;

    // Enviar
    $enviado = $mail->send();

} catch (Exception $e) {
    // Registrar error en log
    error_log("Error al enviar email: {$mail->ErrorInfo}");
    $enviado = false;
}

// ===== ENVIAR EMAIL DE CONFIRMACIÓN AL CLIENTE =====
if ($enviado) {
    try {
        $mailCliente = new PHPMailer(true);

        // Configuración SMTP
        $mailCliente->isSMTP();
        $mailCliente->Host       = SMTP_HOST;
        $mailCliente->SMTPAuth   = true;
        $mailCliente->Username   = SMTP_USER;
        $mailCliente->Password   = SMTP_PASS;
        $mailCliente->SMTPSecure = SMTP_SECURE;
        $mailCliente->Port       = SMTP_PORT;
        $mailCliente->SMTPDebug  = 0;

        // Configuración del email
        $mailCliente->CharSet = 'UTF-8';
        $mailCliente->setFrom($email_remitente, $nombre_empresa);
        $mailCliente->addAddress($email, $nombre);

        // Contenido
        $mailCliente->isHTML(true);
        $mailCliente->Subject = "Confirmación de Solicitud - IBM FINTECH";

        $cuerpo_cliente = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Confirmación de Solicitud</title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f4; padding: 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="background: linear-gradient(135deg, #22499a 0%, #165ea9 100%); padding: 30px; text-align: center;">
                                    <h1 style="color: #ffffff; margin: 0; font-size: 24px;">IBM FINTECH S.A.C.</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 40px 30px;">
                                    <h2 style="color: #22499a; margin: 0 0 20px 0;">¡Gracias por contactarnos!</h2>
                                    <p style="color: #333333; line-height: 1.6; margin-bottom: 20px;">
                                        Estimado/a <strong>' . htmlspecialchars($nombre) . '</strong>,
                                    </p>
                                    <p style="color: #333333; line-height: 1.6; margin-bottom: 20px;">
                                        Hemos recibido su solicitud de asesoría sobre <strong>' . htmlspecialchars($consulta_texto) . '</strong> 
                                        y un asesor experto se pondrá en contacto con usted dentro de las próximas 24 horas.
                                    </p>
                                    <div style="background-color: #f0f9ff; padding: 20px; border-radius: 8px; margin: 30px 0;">
                                        <p style="color: #22499a; margin: 0; text-align: center;">
                                            <strong>📱 WhatsApp:</strong> +51 933 017 232<br>
                                            <strong>📧 Email:</strong> informacion@investmenbm.com
                                        </p>
                                    </div>
                                    <p style="color: #666666; font-size: 14px; line-height: 1.6;">
                                        Saludos cordiales,<br>
                                        <strong>Equipo IBM FINTECH</strong>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #999999;">
                                    <p style="margin: 0;">© 2025 IBM FINTECH S.A.C. - Todos los derechos reservados</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';

        $mailCliente->Body = $cuerpo_cliente;
        $mailCliente->send();

    } catch (Exception $e) {
        // Error al enviar confirmación (no afecta el flujo principal)
        error_log("Error al enviar confirmación: {$mailCliente->ErrorInfo}");
    }
}

// ===== REGISTRO EN LOG =====
$log_file = "contactos.log";
$log_mensaje = date('Y-m-d H:i:s') . " | " . $nombre . " | " . $email . " | " . $telefono . " | " . $consulta_texto . "\n";
file_put_contents($log_file, $log_mensaje, FILE_APPEND);

// ===== REDIRECCIÓN =====
if ($enviado) {
    header("Location: contacto.html");
    exit();
} else {
    header("Location: index.html#contacto");
    exit();
}
?>
