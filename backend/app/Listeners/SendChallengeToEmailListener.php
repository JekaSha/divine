<?php

namespace App\Listeners;

use App\Events\ChallengeSendToEmailEvent;
use App\Mail\ChallengeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\User;
use App\Models\Challenge;
use Illuminate\Support\Str;



class SendChallengeToEmailListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  \App\Events\ChallengeSendToEmailEvent  $event
     * @return void
     */
    public function handle(ChallengeSendToEmailEvent $event)
    {

        $user = $event->user;
        $token = $event->user->remember_token;
        $password = $event->password;
        $link = url("/{$user->language}/step/4/?token={$token}");

        $challenge = Challenge::where('guest_hash', $event->challenge->guest_hash)->get();
        if ($challenge) {
            $challenge = $challenge->first();
            $challenge->user_id = $user->id;
            $challenge->save();
        }

        bb("Generated link: {$link}");

        // Передаём язык в ChallengeMail
        Mail::to($event->email)->queue(new ChallengeMail($challenge, $link, $password, $user->language));
    }
}
