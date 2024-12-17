<?php

namespace App\Events;

use App\Models\Challenge;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChallengeSendToEmailEvent
{
    use Dispatchable, SerializesModels;

    public $challenge;
    public $email;
    public $lang;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Challenge $challenge, string $email, string $lang = "en")
    {
        $this->challenge = $challenge;
        $this->email = $email;
        $this->lang = $lang;
        bb('event:'.$lang);
    }
}
