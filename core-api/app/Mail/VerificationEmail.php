<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class VerificationEmail extends Mailable
{
    public User $user;
    public string $code;

    public function __construct(User $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Verify Your Email')
            ->view('emails.verify')
            ->with([
                'user' => $this->user,
                'verificationUrl' => config('app.frontend_url') . '/verify-email/' . $this->code,
            ]);
    }
}
