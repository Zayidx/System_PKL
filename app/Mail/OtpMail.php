<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
class OtpMail extends Mailable {
  use Queueable, SerializesModels;
  public $otp;
  public function __construct($otp) {
    $this->otp = $otp;
  }
  public function build() {
    return $this->view('emails.otp');
  }
}