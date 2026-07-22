<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $verificationUrl;
    public ?string $password;

    /**
     * NOTE: added $password (nullable) so the "Login Details" box in the
     * template can show it. If you don't want to email a plaintext password,
     * just omit the argument when constructing the mailable — the block is
     * wrapped in @isset($password) and will simply not render.
     */
    public function __construct(User $user, string $verificationUrl, ?string $password = null)
    {
        $this->user = $user;
        $this->verificationUrl = $verificationUrl;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Welcome to Academic Mantra — Thank You for Registering!')
            // FIX: this used to point at 'emails.student-welcome-verify', a view
            // that doesn't exist in your resources/views/emails folder. The
            // actual file is 'emails.student-thank-you' — that mismatch alone
            // would throw a "View not found" error and silently fail to send.
            ->view('emails.student-thank-you')
            ->with([
                'user' => $this->user,
                'verificationUrl' => $this->verificationUrl,
                'password' => $this->password,
                // FIX for the logo: embed it as an inline CID attachment
                // instead of relying on a remote URL. Remote images get
                // blocked by default in Gmail/Outlook/Apple Mail, and many
                // shared-hosting setups (mod_security / hotlink protection)
                // reject image requests that don't come from a real browser,
                // which silently breaks <img src="https://..."> in emails.
                // Embedding guarantees the logo always renders.
                'logoUrl' => $this->embed(public_path('images/logo.png')),
            ]);
    }
}