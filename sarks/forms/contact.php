<?php

$receiving_email_address = 'info@sarks.org';

if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
  include($php_email_form);
} else {
  die('Unable to load the "PHP Email Form" Library!');
}

$contact = new PHP_Email_Form;
$contact->ajax = true;

// reCAPTCHA v3 Validation
if (isset($_POST['recaptcha-response']) && !empty($_POST['recaptcha-response'])) {
  $secret = '6LessB0iAAAAAPSQf18mgiIHPRpZf22T-ZXK8c14;
  $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['recaptcha-response']);
  $responseData = json_decode($verifyResponse);

  if (!$responseData->success || $responseData->score < 0.5) {
    die('reCAPTCHA verification failed. Spam detected.');
  }
} else {
  die('reCAPTCHA verification failed. No token found.');
}

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

// Uncomment below code if you want to use SMTP to send emails. You need to enter your correct SMTP credentials

$contact->smtp = array(
  'host' => 'smtp.zoho.com',
  'username' => 'info@sarks.org',
  'password' => 'Q7aVrzHq2Lzt',
  'port' => '587'
);


$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

echo $contact->send();
