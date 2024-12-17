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

        $password = Str::random(16);

        try {
            $user = User::firstOrCreate(
                ['email' => $event->email], // Check by email
                [
                    'name' => 'Guest User',
                    'password' => bcrypt($password), // Password set only when creating a new user
                ]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // Unique constraint violation
                $user = User::where('email', $event->email)->first();
            } else {
                throw $e; // Re-throw other exceptions
            }
        }

        $token = Str::random(32);

        $user->remember_token = $token;
        $user->save();

        $link = url("/{$event->lang}/step/4/?token={$token}");

        $challenge = Challenge::where('guest_hash', $event->challenge->guest_hash)->get();
        if ($challenge) {
            $challenge = $challenge->first();
            $challenge->user_id = $user->id;
            $challenge->save();
        }

        bb("Generated link: {$link}");

        // Передаём язык в ChallengeMail
        Mail::to($event->email)->queue(new ChallengeMail($challenge, $link, $password, $event->lang));
    }
}
