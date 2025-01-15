<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Services\UserService;
use App\Services\ChallengeService;
use App\Models\Challenge;

use Illuminate\Support\Facades\Mail;
use App\Mail\ChallengeMail;


class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update the status of transactions';

    protected $challengeService;
    public function __construct(ChallengeService $challengeService)
    {
        parent::__construct();
        $this->challengeService = $challengeService;

    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Test Start");

        $email = "ishaposhnyk@yahoo.com";
        try {
        Mail::raw('This is a test email', function ($message) use ($email) {
            $message->to($email)
                ->subject('Test Email from Laravel');
        });
        } catch (\Exception $e) {
            logger()->error('Email failed: ' . $e->getMessage());
        }
die;

        $challenge =  Challenge::find(1);

        Log::info("Challenge fetched successfully", ['challenge_id' => $challenge->id]);

        // Preparing data for the email
        $lang = 'en'; // Example language
        $password = 'test-password'; // Example password
        $link = 'https://example.com/challenge/1'; // Example link

        // Sending the email
        Mail::to('shaposhnyk@gmail.com')->send(new ChallengeMail($challenge, $link, $password, $lang));



        Log::info("Test command finished successfully");
    }
}
