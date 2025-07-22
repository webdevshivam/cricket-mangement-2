<?php
// app/Controllers/MailTest.php
namespace App\Controllers;

use CodeIgniter\Controller;

class MailTest extends Controller
{
  public function send()
  {
    helper('email');


    $recipients = [
      ['email' => 'webdev.shivamkushwah@gmail.com', 'name' => 'Shivam'],
      ['email' => 'helloyourwebsitedesign@gmail.com', 'name' => 'Deepak'],
      ['email' => 'deepakthakurjms2@gmail.com
', 'name' => 'Deepak']
    ];

    $successCount = 0;
    $failedCount = 0;

    foreach ($recipients as $person) {
      $message = "<h3>Hello {$person['name']}</h3><p>This is a personalized message from MegaStar League.</p>";

      $result = sendCustomMail(
        $person['email'],
        'MegaStar League Update',
        $message,
        'info@megastarpremiercricketleague.com',
        'MegaStar League'
      );

      if ($result === true) {
        $successCount++;
      } else {
        $failedCount++;
        echo "❌ Failed to send to {$person['email']}<br><pre>$result</pre><hr>";
      }

      // 🕒 Optional: Sleep between mails to avoid SMTP throttling
      usleep(300000); // 0.3 seconds pause
    }

    echo "<br>✅ Sent: $successCount<br>❌ Failed: $failedCount";
  }
}
