<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

//setlocale(LC_ALL, 'en_US.UTF-8');
 header('Content-type: text/javascript; charset=utf-8');
 
session_start();


// Asegúrate de que las rutas a FPDF y FPDI sean correctas según tu estructura.
//require_once('lib/fpdf/fpdf.php'); // Ajusta si es necesario
//require_once('lib/fpdi/src/autoload.php'); // Para FPDI v2 o superior que usa autoloading
require ('/home/u124132715/domains/prepenv.com/public_html/SOSMEX/vendor/autoload.php');
// Si usas una versión más antigua de FPDI que no tiene autoload.php en src, podrías necesitar:
// require_once('lib/fpdi/fpdi.php');

use setasign\Fpdi\Fpdi;

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_full_name'])) {
    die("Acceso no autorizado o sesión no válida.");
}

$userName = $_SESSION['user_full_name'];
$courseName = "Curso de Capacitación en Línea"; // Puedes hacerlo dinámico si lo deseas
$completionDate = date("d/m/Y");
$templateFile = 'M1.pdf'; // Asegúrate que este archivo exista

if (!file_exists($templateFile)) {
    die("Error: Archivo de plantilla PDF no encontrado: " . $templateFile);
}

try {
    $pdf = new Fpdi();

    // Importar la primera página de la plantilla M1.pdf
    $pageCount = $pdf->setSourceFile($templateFile);
    $templateId = $pdf->importPage(1);
    $size = $pdf->getTemplateSize($templateId);

    // Añadir una página al nuevo PDF con las dimensiones de la plantilla
    if ($size['width'] > $size['height']) {
        $pdf->AddPage('L', [$size['width'], $size['height']]);
    } else {
        $pdf->AddPage('P', [$size['width'], $size['height']]);
    }

    // Usar la plantilla importada
    $pdf->useTemplate($templateId);

    // Configurar fuente y color
    // Necesitarás ajustar la fuente, tamaño y coordenadas (X, Y)
    // según el diseño de tu plantilla M1.pdf.
    // Estos son valores de ejemplo.

    // Escribir el nombre del participante
    $pdf->SetFont('Arial', 'B', 20); // Ejemplo: Arial Bold, tamaño 20
    $pdf->SetTextColor(0, 0, 0); // Negro
    $pdf->SetXY(50, 100); // Ejemplo: Coordenada X=50mm, Y=100mm desde la esquina superior izquierda
    $pdf->Write(0, utf8_decode($userName));

    // Escribir la fecha de finalización
    $pdf->SetFont('Arial', '', 12); // Ejemplo: Arial Regular, tamaño 12
    $pdf->SetXY(150, 120); // Ejemplo: Coordenada X=150mm, Y=120mm
    $pdf->Write(0, $completionDate);

    // Escribir el nombre del curso (opcional, si tu plantilla tiene espacio)
    // $pdf->SetFont('Arial', 'I', 14); // Ejemplo: Arial Italic, tamaño 14
    // $pdf->SetXY(50, 140); // Ejemplo
    // $pdf->Write(0, utf8_decode($courseName));

    $pdf->Output('D', 'Certificado-' . str_replace(' ', '_', $userName) . '.pdf'); // 'D' para forzar descarga
} catch (Exception $e) {
    die("Error al generar el PDF: " . $e->getMessage());
}
?>