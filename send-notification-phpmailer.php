<?php
// send-notification-phpmailer.php - Email notification using PHPMailer (Alternative version)
// Upload this file to your aapanel website folder

// Download PHPMailer from: https://github.com/PHPMailer/PHPMailer
// Extract and upload the 'src' folder to your website

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Set headers for CORS (allow cross-origin requests)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    // Get POST data
    $to_email = $_POST['to_email'] ?? 'teguhgunawan21@gmail.com';
    $from_name = $_POST['from_name'] ?? '';
    $from_email = $_POST['from_email'] ?? '';
    $subject = $_POST['subject'] ?? 'PENDAFTARAN WEBINAR MENTORING UMROH';
    
    $nama = $_POST['nama'] ?? '';
    $whatsapp = $_POST['whatsapp'] ?? '';
    $email = $_POST['email'] ?? '';
    $sapaan = $_POST['sapaan'] ?? '';
    $asal_kota = $_POST['asal_kota'] ?? '';
    $status = $_POST['status'] ?? '';
    $agent_id = $_POST['agent_id'] ?? '';
    $waktu_daftar = $_POST['waktu_daftar'] ?? date('Y-m-d H:i:s');
    
    // Validate required fields
    if (empty($nama) || empty($whatsapp)) {
        throw new Exception('Missing required fields');
    }
    
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    // SMTP Configuration for Niagahoster with debugging
    $mail->isSMTP();
    $mail->Host       = 'srv142.niagahoster.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'admin@pejuangumroh.id';
    $mail->Password   = 'YOUR_EMAIL_PASSWORD_HERE'; // Ganti dengan password email Anda
    
    // Try different encryption methods
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL on port 465
    $mail->Port       = 465;
    
    // Debug disabled for production
    $mail->SMTPDebug  = 0; // No debug output
    
    // Additional options for problematic servers
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    // Email settings
    $mail->setFrom('admin@pejuangumroh.id', 'Mentoring Umroh');
    $mail->addAddress($to_email);
    $mail->addReplyTo($email ?: 'admin@pejuangumroh.id', $nama ?: 'Mentoring Umroh');
    
    // Content
    $mail->isHTML(false); // Set email format to plain text
    $mail->Subject = $subject;
    $mail->Body = "PENDAFTARAN WEBINAR BARU!

Detail Pendaftar:
====================
Nama: $nama
WhatsApp: $whatsapp
Email: $email
Sapaan: $sapaan
Asal Kota: $asal_kota
Status: $status

Agent ID: $agent_id
Produk Terdaftar: 10 produk umroh

Waktu Daftar: $waktu_daftar

Terima kasih!
====================";
    
    // Send email
    $mail->send();
    
    // Log successful email
    error_log("PHPMailer: Email sent successfully to $to_email for $nama");
    
    echo json_encode([
        'success' => true,
        'message' => 'Email notification sent successfully via PHPMailer',
        'to' => $to_email,
        'subject' => $subject,
        'smtp_server' => 'srv142.niagahoster.com',
        'method' => 'PHPMailer'
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log("PHPMailer Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'method' => 'PHPMailer',
        'smtp_config' => [
            'host' => 'srv142.niagahoster.com',
            'port' => 465,
            'username' => 'admin@pejuangumroh.id'
        ]
    ]);
}
?> 