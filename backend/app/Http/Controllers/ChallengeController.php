<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChallengeService;
use App\Repositories\ChallengeRepository;
use App\Events\ChallengeSendToEmailEvent;

class ChallengeController extends Controller
{
    protected $challengeService;
    protected $challengeRepository;
    protected ?array $user = null;
    public function __construct(
        Request $request,
        ChallengeService $challengeService,
        ChallengeRepository $challengeRepository
    ) {

        parent::__construct($request);
        $this->challengeService = $challengeService;
        $this->challengeRepository = $challengeRepository;
    }
    public function get()
    {
        $challenges = $this->challengeService->get($this->identifier);
        return $challenges;

    }

    public function store(Request $r, int $prompt_id, string $session_hash) {

        $data = $r->all();
        $data['prompt_id'] = $prompt_id;
        $data['session_hash'] = $session_hash;

        $challenge = $this->challengeService->request($this->identifier, $data);
        return $challenge;
    }

    public function sendToEmail(Request $r, string $session_hash) {
        $email = $r->email;
        $lang = $this->lang;
        $challenge = $this->challengeRepository->get($this->identifier,
            [
                'session_hash' => $session_hash,
                'sort_order' => 'desc',
            ]
        )->first();

        if ($email) {
            event(new ChallengeSendToEmailEvent($challenge, $email, $lang));
        }
        return $challenge;
    }

    public function getSession(Request $r, string $session_hash) {

        $chat = $this->challengeRepository->get($this->identifier,
            [
                'user_id' => $this->user['id'],
                'session_hash' => $session_hash,
                'sort_by' => "id",
                'sort_order' => 'desc',
            ]
        );

        $r = ['status' => 'success', "data" => ["user" => $this->user, "chat" => $chat]];

        return $r;
    }

    public function generateLink(Request $request)
    {
        $email = $request->input('email');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Guest User',
                'language' => $this->lang,
            ]
        );


        $token = Str::random(32);

        $user->update(['remember_token' => $token]);

        $link = url("/auth/validate?token={$token}");

        return response()->json(['link' => $link]);
    }

    public function validateToken(Request $request)
    {
        $token = $request->query('token');

        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return response()->json([
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function getPackages() {
        $packages = $this->challengeService->getPackages(['status' => 'active', 'type' => 'requests_per_month']);
        $data = ['status' => 'success', 'data' => ['packages' => $packages]];
        return $data;
    }

}
