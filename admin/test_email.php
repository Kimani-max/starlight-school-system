<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// If using Composer
// Or include PHPMailer manually:
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';
require '../PHPMailer/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2; // Verbose debug output

    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'starlight285@outlook.com'; // Replace with your Gmail
    $mail->Password = 'unkbpfvtkiopcikq';  // Use App Password (NOT Gmail password)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Fix SSL certificate issue
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
            'cafile'            => __DIR__ . '\\cacert.pem',
    ]
];

    // Recipients
    $mail->setFrom('starlight285@outlook.com', 'Starlight Institute');
    $mail->addAddress('wanjirukimani631@example.com', 'Test User'); // Change this

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Admission Email';
    $mail->Body    = '<strong>This is a test email</strong> sent using Gmail SMTP and PHPMailer.';
    $mail->AltBody = 'This is a test email sent using Gmail SMTP and PHPMailer.';

    $mail->send();
    echo '✅ Message has been sent successfully';
} catch (Exception $e) {
    echo "❌ Mailer Error: {$mail->ErrorInfo}";
}
