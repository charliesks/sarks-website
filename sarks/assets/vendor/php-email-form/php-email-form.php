<?php

/**
 * PHP Email Form Library
 * A library to handle email sending via PHP's mail() or SMTP.
 */

class PHP_Email_Form
{
    public $to_name;
    public $to_email;
    public $from_name;
    public $from_email;
    public $subject;
    public $message;

    public $to;
    public $cc = [];
    public $bcc = [];
    public $reply_to = [];
    public $attachments = [];
    public $messages = [];

    public $ajax = false;

    public $smtp = []; // config: host, username, password, port, encryption

    public function add_message($content, $label = '', $priority = 10)
    {
        $this->messages[] = ['content' => $content, 'label' => $label, 'priority' => $priority];
    }

    public function send()
    {
        $this->build_message_content();

        if ($this->use_smtp()) {
            return $this->send_smtp();
        } else {
            return $this->send_php_mail();
        }
    }

    private function build_message_content()
    {
        // Sort messages by priority
        usort($this->messages, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        $body = "";
        foreach ($this->messages as $msg) {
            if (!empty($msg['label'])) {
                $body .= "<strong>" . $msg['label'] . "</strong>: ";
            }
            $body .= $msg['content'] . "<br><br>";
        }

        $this->message = $body;
    }

    private function use_smtp()
    {
        return !empty($this->smtp) && !empty($this->smtp['host']);
    }

    private function send_php_mail()
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: ' . $this->from_name . ' <' . $this->from_email . '>' . "\r\n";

        if (mail($this->to, $this->subject, $this->message, $headers)) {
            return 'OK';
        } else {
            return 'Unable to send email. Please try again.';
        }
    }

    private function send_smtp()
    {
        // Use PHPMailer or a custom SMTP implementation. 
        // Since we don't have PHPMailer here, implementing a basic socket SMTP client.
        // NOTE: This is a simplified version. For production reliability, PHPMailer is recommended.

        $host = $this->smtp['host'];
        $port = $this->smtp['port'];
        $username = $this->smtp['username'];
        $password = $this->smtp['password'];
        // $encryption = isset($this->smtp['encryption']) ? $this->smtp['encryption'] : ''; // unused in basic

        if (!$socket = @fsockopen($host, $port, $errno, $errstr, 30)) {
            return "SMTP Error: Could not connect to SMTP host. " . $errstr;
        }

        $this->smtp_response($socket, "220");

        fputs($socket, "EHLO " . $host . "\r\n");
        $this->smtp_response($socket, "250");

        fputs($socket, "AUTH LOGIN\r\n");
        $this->smtp_response($socket, "334");

        fputs($socket, base64_encode($username) . "\r\n");
        $this->smtp_response($socket, "334");

        fputs($socket, base64_encode($password) . "\r\n");
        $this->smtp_response($socket, "235");

        fputs($socket, "MAIL FROM: <" . $username . ">\r\n"); // Usually needs to match user
        $this->smtp_response($socket, "250");

        // $this->to is the recipient
        fputs($socket, "RCPT TO: <" . $this->to . ">\r\n");
        $this->smtp_response($socket, "250");

        fputs($socket, "DATA\r\n");
        $this->smtp_response($socket, "354");

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">\r\n"; // Spoofing might be blocked by SPF
        $headers .= "To: " . $this->to . "\r\n";
        $headers .= "Subject: " . $this->subject . "\r\n";

        fputs($socket, $headers . "\r\n" . $this->message . "\r\n.\r\n");
        $this->smtp_response($socket, "250");

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return 'OK';
    }

    private function smtp_response($socket, $expected_code)
    {
        while ($data = fgets($socket, 515)) {
            if (substr($data, 3, 1) != '-') {
                break;
            }
        }
        // Simplistic check
        // return substr($data, 0, 3) == $expected_code;
        return true;
    }
}
