<?php
// send-notification.php - Email notification handler for Mentoring Umroh Registration
// Upload this file to your aapanel website folder

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

// SMTP Configuration for Niagahoster
$smtp_config = [
    'host' => 'srv142.niagahoster.com',
    'port' => 465,
    'encryption' => 'ssl',
    'username' => 'admin@pejuangumroh.id',
    'password' => 'YOUR_EMAIL_PASSWORD_HERE', // Ganti dengan password email Anda
    'from_email' => 'admin@pejuangumroh.id',
    'from_name' => 'Mentoring Umroh'
];

// Simple SMTP function
function sendSMTPEmail($to, $subject, $message, $config) {
    $socket = fsockopen("ssl://" . $config['host'], $config['port'], $errno, $errstr, 30);
    
    if (!$socket) {
        throw new Exception("Cannot connect to SMTP server: $errstr ($errno)");
    }
    
    $response = fgets($socket, 515);
    if (substr($response, 0, 3) != '220') {
        throw new Exception("SMTP connection failed: $response");
    }
    
    // EHLO
    fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
    $response = fgets($socket, 515);
    
    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    $response = fgets($socket, 515);
    
    fputs($socket, base64_encode($config['username']) . "\r\n");
    $response = fgets($socket, 515);
    
    fputs($socket, base64_encode($config['password']) . "\r\n");
    $response = fgets($socket, 515);
    
    if (substr($response, 0, 3) != '235') {
        throw new Exception("SMTP authentication failed: $response");
    }
    
    // MAIL FROM
    fputs($socket, "MAIL FROM: <" . $config['from_email'] . ">\r\n");
    $response = fgets($socket, 515);
    
    // RCPT TO
    fputs($socket, "RCPT TO: <$to>\r\n");
    $response = fgets($socket, 515);
    
    // DATA
    fputs($socket, "DATA\r\n");
    $response = fgets($socket, 515);
    
    // Email headers and body
    $email_data = "From: " . $config['from_name'] . " <" . $config['from_email'] . ">\r\n";
    $email_data .= "To: $to\r\n";
    $email_data .= "Subject: $subject\r\n";
    $email_data .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $email_data .= "Date: " . date('r') . "\r\n";
    $email_data .= "\r\n";
    $email_data .= $message . "\r\n";
    $email_data .= ".\r\n";
    
    fputs($socket, $email_data);
    $response = fgets($socket, 515);
    
    // QUIT
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    return substr($response, 0, 3) == '250';
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
    
    // Create email content
    $message = "PENDAFTARAN WEBINAR BARU!

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
    
    // Send email using SMTP
    $mail_sent = sendSMTPEmail($to_email, $subject, $message, $smtp_config);
    
    if ($mail_sent) {
        // Log successful email (optional)
        error_log("SMTP Email sent successfully to $to_email for $nama");
        
        echo json_encode([
            'success' => true,
            'message' => 'Email notification sent successfully via SMTP',
            'to' => $to_email,
            'subject' => $subject,
            'smtp_server' => $smtp_config['host']
        ]);
    } else {
        throw new Exception('Failed to send email via SMTP');
    }
    
} catch (Exception $e) {
    // Log error
    error_log("SMTP Email error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'smtp_config' => [
            'host' => $smtp_config['host'],
            'port' => $smtp_config['port'],
            'username' => $smtp_config['username']
        ]
    ]);
}
?> 