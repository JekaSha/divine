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

    /**
     * Create a new message instance.
     *
     * @param Challenge $challenge
     * @param string $lang
     * @return void
     */
    public function __construct(Challenge $challenge, string $password, string $lang = 'en')
    {
        $this->challenge = $challenge;
        $this->lang = $lang;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Установить текущую локаль для приложения
        app()->setLocale($this->lang);

        // Локализованный заголовок
        $subject = __('email.challenge_subject');

        // Выбор шаблона из соответствующей папки
        $viewPath = "emails.{$this->lang}.challenge";

        return $this->subject($subject)
            ->markdown($viewPath, [
                'challenge' => $this->challenge,
                'password' => $this->password,
            ]);
    }
}
