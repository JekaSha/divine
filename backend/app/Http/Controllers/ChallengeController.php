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

        $r = ['status' => 'error', 'msg' => 'no response'];
        if ($challenge['response']) {
            $premium = $this->userService->tm("expired_date");

            if (!$premium) {
                $trimmed = $this->trimResponseAndCalculate($challenge['response']);
                $challenge['response'] = $trimmed['trimmed'];
                $challenge['percentages'] = $trimmed['percentages'];
                $challenge['response_original_length'] = $trimmed['original_length'];
            }

            $r = ['status' => "success", "data" => $challenge];
        }

        return $r;
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

        $data = ["status" => "success", "data" => $challenge];

        if ($email) {
            event(new ChallengeSendToEmailEvent($challenge, $email, $lang));
        }
        return $data;
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

        $premium = $this->userService->tm("expired_date");

        if (!$premium) {
            $chat = $chat[0] ?? null;
            if ($chat) {
                $r = $this->trimResponseAndCalculate($chat['response']);
                $percentages = $r['percentages'];
                $chat['response'] = $r['trimmed'];
                $chat['response_original_length'] = $r['original_length'];
                $chat = [$chat];
            }
        } else {
            $percentages = [];
        }

        $r = ['status' => 'success', "data" => ["user" => $this->user, "chat" => $chat, "percentages" => $percentages]];

        return $r;
    }


    /**
     * Trims the response and calculates character percentages.
     *
     * @param string $response
     * @return array
     */
    private function trimResponseAndCalculate(string $response): array
    {
        $totalLength = strlen($response);

        $thresholds = [150, 250, 750];
        $maxLimit = end($thresholds);

        $trimmedResponse = substr($response, 0, $maxLimit);

        $percentages = [];
        foreach ($thresholds as $limit) {
            $percent = round(($limit / $totalLength) * 100, 2);
            $percentages[$limit] = $percent;
        }

        return [
            'trimmed' => $trimmedResponse,
            'percentages' => $percentages,
            'original_length' => $totalLength,
        ];
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
    

}
