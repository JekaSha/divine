<?php

namespace App\Mail;

use App\Models\Challenge;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChallengeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $challenge;
    public $lang;
    public $password;
    public $link;

    /**
     * Create a new message instance.
     *
     * @param Challenge $challenge
     * @param string $lang
     * @return void
     */
    public function __construct(Challenge $challenge, string $link, string $password, string $lang = 'en')
    {
        $this->challenge = $challenge;
        $this->lang = $lang;
        $this->password = $password;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->lang);

        $subject = __('email.challenge_subject');

        $viewPath = "emails.{$this->lang}.challenge";
    
        return $this->subject($subject)
            ->markdown($viewPath, [
                'challenge' => $this->challenge,
                'password' => $this->password,
                'link' => $this->link,
            ]);
    }
}
