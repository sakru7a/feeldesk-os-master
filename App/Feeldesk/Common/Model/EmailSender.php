<?php

namespace Common\Model;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

class EmailSender
{
    private $host = 'smtp.em.dingtalk.com';
    private $username = 'mayao@zdpress.top';
    private $password = 'h7GfC5fsKi7MM6la';
    private $port = 465;
    private $secure = 'ssl';

    public function sendMail($to, $subject, $body, $cc = null)
    { 
        $mail = new PHPMailer(true);
        try {
            // Configure SMTP server settings
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->secure;
            $mail->Port = $this->port;

            // Set sender and recipient
            $mail->setFrom($this->username, 'Support Team');
            $mail->addAddress($to);

            // Add CC recipients if provided
            if ($cc && is_array($cc)) {
                foreach ($cc as $ccAddress) {
                    $mail->addCC($ccAddress);
                }
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Send email
            $mail->send();
            error_log("Message sent successfully.");
            return ['status' => 1, 'message' => '邮件已发送成功'];
        } catch (Exception $e) {
            error_log("Failed to send message: " . $e->getMessage());
            return ['status' => 0, 'message' => "邮件发送失败: {$mail->ErrorInfo}"];
        }
    }
}
