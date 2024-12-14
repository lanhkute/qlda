<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use SerializesModels;

    public $name;
    public $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $otp)
    {
        $this->name = $name;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("no-reply@luongtuan.xyz")->view('auth.otp')
            ->with([
                'name' => $this->name,
                'otp' => $this->otp
            ])
            ->subject('Mã xác thực OTP');
    }
}