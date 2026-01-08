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

    public function add_attachment($path, $name = "")
    {
        if (file_exists($path)) {
            $this->attachments[] = ['path' => $path, 'name' => $name ? $name : basename($path)];
            return true;
        }
        return false;
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
        $boundary = md5(time());
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= 'From: ' . $this->from_name . ' <' . $this->from_email . '>' . "\r\n";

        if (empty($this->attachments)) {
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $full_body = $this->message;
        } else {
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"" . "\r\n";
            $full_body = "--" . $boundary . "\r\n";
            $full_body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $full_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $full_body .= $this->message . "\r\n\r\n";

            foreach ($this->attachments as $attachment) {
                $filename = $attachment['name'];
                $path = $attachment['path'];
                $content = file_get_contents($path);
                $content = chunk_split(base64_encode($content));

                $full_body .= "--" . $boundary . "\r\n";
                $full_body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
                $full_body .= "Content-Description: " . $filename . "\r\n";
                $full_body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"; size=" . filesize($path) . ";\r\n";
                $full_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $full_body .= $content . "\r\n\r\n";
            }
            $full_body .= "--" . $boundary . "--";
        }

        if (mail($this->to, $this->subject, $full_body, $headers)) {
            return 'OK';
        } else {
            return 'Unable to send email. Please try again.';
        }
    }

    private function send_smtp()
    {
        $host = $this->smtp['host'];
        $port = $this->smtp['port'];
        $username = $this->smtp['username'];
        $password = $this->smtp['password'];

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

        fputs($socket, "MAIL FROM: <" . $username . ">\r\n");
        $this->smtp_response($socket, "250");

        fputs($socket, "RCPT TO: <" . $this->to . ">\r\n");
        $this->smtp_response($socket, "250");

        fputs($socket, "DATA\r\n");
        $this->smtp_response($socket, "354");

        $boundary = md5(time());
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "From: " . $this->from_name . " <" . $this->from_email . ">\r\n";
        $headers .= "To: " . $this->to . "\r\n";
        $headers .= "Subject: " . $this->subject . "\r\n";

        if (empty($this->attachments)) {
            $headers .= "Content-type: text/html; charset=utf-8\r\n";
            $full_body = $this->message;
        } else {
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"\r\n";
            $full_body = "--" . $boundary . "\r\n";
            $full_body .= "Content-Type: text/html; charset=UTF-8\r\n";
            $full_body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $full_body .= $this->message . "\r\n\r\n";

            foreach ($this->attachments as $attachment) {
                $filename = $attachment['name'];
                $path = $attachment['path'];
                $content = file_get_contents($path);
                $content = chunk_split(base64_encode($content));

                $full_body .= "--" . $boundary . "\r\n";
                $full_body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n";
                $full_body .= "Content-Description: " . $filename . "\r\n";
                $full_body .= "Content-Disposition: attachment; filename=\"" . $filename . "\"; size=" . filesize($path) . ";\r\n";
                $full_body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $full_body .= $content . "\r\n\r\n";
            }
            $full_body .= "--" . $boundary . "--";
        }

        fputs($socket, $headers . "\r\n" . $full_body . "\r\n.\r\n");
        $this->smtp_response($socket, "250");

        fputs($socket, "QUIT\r\n");
        fclose($socket);

        return 'OK';
    }

    private function smtp_response($socket, $expected_code)
    {
        $response = "";
        while ($data = fgets($socket, 515)) {
            $response .= $data;
            if (substr($data, 3, 1) != '-') {
                break;
            }
        }
        return true;
    }
}
