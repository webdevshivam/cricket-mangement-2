<?php

/**
 * Send email using Hostinger SMTP with optional CC, BCC and attachments.
 *
 * @param string|array $to
 * @param string $subject
 * @param string $message
 * @param string $from
 * @param string $fromName
 * @param array|null $cc
 * @param array|null $bcc
 * @param string|array|null $attachments - File path or array of file paths
 * @return bool|string
 */
function sendCustomMail(
  $to,
  $subject,
  $message,
  $from = 'info@megastarpremiercricketleague.com',
  $fromName = 'MegaStar League',
  $cc = null,
  $bcc = null,
  $attachments = null
) {
  $email = \Config\Services::email();

  $config = [
    'protocol'  => 'smtp',
    'SMTPHost'  => 'smtp.hostinger.com',
    'SMTPUser'  => 'info@megastarpremiercricketleague.com',
    'SMTPPass'  => '9012442784@Jms@123',
    'SMTPPort'  => 465,
    'SMTPCrypto' => 'ssl',
    'mailType'  => 'html',
    'charset'   => 'utf-8',
    'wordWrap'  => true,
  ];

  $email->initialize($config);
  $email->setTo($to);
  if ($cc) $email->setCC($cc);
  if ($bcc) $email->setBCC($bcc);
  $email->setFrom($from, $fromName);
  $email->setSubject($subject);
  $email->setMessage($message);

  // Handle single or multiple attachments
  if ($attachments) {
    if (is_array($attachments)) {
      foreach ($attachments as $file) {
        if (file_exists($file)) {
          $email->attach($file);
        }
      }
    } elseif (file_exists($attachments)) {
      $email->attach($attachments);
    }
  }

  if ($email->send()) {
    return true;
  } else {
    return $email->printDebugger(['headers']);
  }
}
