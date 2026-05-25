<?php
// includes/mailer.php — Helper kirim email via SMTP Gmail (PHPMailer)

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Kirim email balasan ke pengirim pesan.
 *
 * @param string $toEmail   Alamat email penerima
 * @param string $toName    Nama penerima
 * @param string $subject   Subject email
 * @param string $body      Isi pesan (plain text — akan di-convert ke HTML)
 * @return true|string      true jika berhasil, string pesan error jika gagal
 */
function sendReplyEmail(string $toEmail, string $toName, string $subject, string $body): true|string
{
    $s = getAllSettings();

    $gmail       = trim($s['smtp_gmail']        ?? '');
    $appPassword = trim($s['smtp_app_password'] ?? '');
    $fromName    = trim($s['smtp_from_name']    ?? ($s['site_name'] ?? 'Admin'));
    $replyTo     = trim($s['smtp_reply_to']     ?? '');

    if (!$gmail || !$appPassword) {
        return 'SMTP belum dikonfigurasi. Isi Gmail & App Password di Pengaturan → Email SMTP.';
    }

    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        return 'Alamat email penerima tidak valid: ' . $toEmail;
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmail;
        $mail->Password   = $appPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        $mail->Timeout    = 15;

        // Pengirim
        $mail->setFrom($gmail, $fromName);
        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL) && $replyTo !== $gmail) {
            $mail->addReplyTo($replyTo, $fromName);
        }

        // Penerima
        $mail->addAddress($toEmail, $toName);

        // Konten email — HTML + plain text fallback
        $mail->isHTML(true);
        $mail->Subject = $subject;

        // Buat HTML body yang rapi
        $siteName   = htmlspecialchars($s['site_name'] ?? 'Admin');
        $bodyHtml   = nl2br(htmlspecialchars($body));
        $year       = date('Y');
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:32px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <!-- Header -->
        <tr><td style="background:#1B4F8A;padding:24px 32px;">
          <p style="margin:0;color:#ffffff;font-size:18px;font-weight:700;">{$siteName}</p>
        </td></tr>
        <!-- Body -->
        <tr><td style="padding:32px;">
          <p style="margin:0 0 16px;font-size:15px;color:#1e293b;">Yth. <strong>{$toName}</strong>,</p>
          <div style="font-size:14px;color:#334155;line-height:1.8;">{$bodyHtml}</div>
        </td></tr>
        <!-- Footer -->
        <tr><td style="padding:20px 32px;background:#f8fafc;border-top:1px solid #e2e8f0;">
          <p style="margin:0;font-size:12px;color:#94a3b8;">&copy; {$year} {$siteName}. Email ini dikirim sebagai balasan atas pesan yang Anda kirimkan melalui website kami.</p>
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
        $mail->AltBody = $body; // Plain text fallback

        $mail->send();
        return true;
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}

/**
 * Test koneksi SMTP — dipakai dari halaman settings (opsional).
 */
function testSmtpConnection(): true|string
{
    $s = getAllSettings();
    $gmail       = trim($s['smtp_gmail']        ?? '');
    $appPassword = trim($s['smtp_app_password'] ?? '');

    if (!$gmail || !$appPassword) {
        return 'Gmail dan App Password belum diisi.';
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $gmail;
        $mail->Password   = $appPassword;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->Timeout    = 10;
        $mail->SMTPDebug  = SMTP::DEBUG_OFF;

        // Hanya test koneksi, tidak kirim email
        if ($mail->smtpConnect()) {
            $mail->smtpClose();
            return true;
        }
        return 'Koneksi SMTP gagal.';
    } catch (Exception $e) {
        return $mail->ErrorInfo;
    }
}