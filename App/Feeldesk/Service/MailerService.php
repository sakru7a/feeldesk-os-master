<?php
namespace App\Feeldesk\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailerService
{
    private $host = 'mail.repqj.com';  // SMTP 服务器地址
    private $username = 'office@repqj.com';  // SMTP 用户名
    private $password = 'office@repqj.com';  // SMTP 密码
    private $port = 465;  // SMTP 端口
    private $secure = 'tls';  // 加密方式

    public function sendEmail($to, $subject, $body)
    {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            $mail->SMTPSecure = $this->secure;
            $mail->Port = $this->port;

            // Recipients
            $mail->setFrom($this->username, 'Support Team');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
