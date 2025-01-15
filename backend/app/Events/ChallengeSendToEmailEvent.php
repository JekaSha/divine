<?php

namespace App\Events;

use App\Models\Challenge;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ChallengeSendToEmailEvent
{
    use Dispatchable, SerializesModels;

    public $challenge;
    public $user;
    public $password;
    public $created;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Challenge $challenge, User $user, string $password, $created = false)
    {
        $this->challenge = $challenge;
        $this->user = $user;
        $this->password = $password;
        $this->created = $created;

    }
}
