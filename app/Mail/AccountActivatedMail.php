<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        $companyName = 'Shift Roster';
        return $this->from('supports@technorizen.com', $companyName)
                    ->subject("Account Activated - $companyName")
                    ->view('admin.emails.account_activated'); // Blade view
    }
}