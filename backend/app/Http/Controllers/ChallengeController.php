<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ChallengeService;
use App\Repositories\ChallengeRepository;
use App\Events\ChallengeSendToEmailEvent;

use Illuminate\Support\Str;
use App\Models\User;

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
    public function get(Request $r, int $id)
    {
        $filter = $r->all();
        $filter['id'] = $id;
        $challenges = $this->challengeService->get($this->identifier, $filter);
        $r = ['status' => 'success', "data" => ["challenges" => $challenges]];
        return $r;

    }

    public function request(Request $r, string $session_hash) {

        $data = $r->all();
        $data['session_hash'] = $session_hash;

        $challenges = $this->challengeService->request($this->identifier, $data);

        $r = ['status' => 'success', "data" => ['msg' => 'The task have been queued.', 'challenges' => $challenges]];
        /*
        if ($challenge['response']) {
            if ($this->userService) {
                $premium = $this->userService->tm("expired_date");
            } else {
                $premium = false;
            }
            if (!$premium) {
                $trimmed = $this->trimResponseAndCalculate($challenge['response']);
                $challenge['response'] = $trimmed['trimmed'];
                $challenge['percentages'] = $trimmed['percentages'];
                $challenge['response_original_length'] = $trimmed['original_length'];
            }

            $r = ['status' => "success", "data" => $challenge];
        }
*/
        return $r;
    }
    private function queueChallengeRequest(array $data): void
    {
        // Логика для помещения задачи в очередь
        dispatch(function () use ($data) {
            $this->challengeService->request($this->identifier, $data);
        });
    }

    private function prepareData(array $requestData, int $prompt_id, string $session_hash): array
    {
        $requestData['prompt_id'] = $prompt_id;
        $requestData['session_hash'] = $session_hash;

        return $requestData;
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

        $data = ["status" => "success", "data" => ["challenge" => $challenge]];

        if ($email) {

            $password = Str::random(16);

            $created = false;
            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'name' => $email,
                    'language' => $lang,
                    'password' => bcrypt($password),
                ]);
                $created = true;
            } else {
                $created = false;
            }

            $token = Str::random(32);

            $user->remember_token = $token;
            $user->save();

            event(new ChallengeSendToEmailEvent($challenge, $user, $password, $created));

            $data['data']['user'] = $user;
            $data['data']['token'] = $token;
            $data['data']['user_created'] = $created;
        }
        return $data;
    }

    public function getSession(Request $r, string $session_hash) {

        $data = [
            'user_id' => $this->user['id'],
            'session_hash' => $session_hash,
            'sort_by' => "id",
            'sort_order' => 'desc',
        ];
        if ($r->id) {
            unset($data['session_hash']);
            $data['id'] = $r->id;
        }

        $chat = $this->challengeRepository->get($this->identifier,
            $data
        );

        $premium = $this->userService ? $this->userService->tm("expired_date") : false;
        $permissions = [];
        $percentages = [];
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
            $permissions = $this->userService->getPermissions();
            $permissions['active'] = $this->userService->isPermissionActive();
        }

        $r = ['status' => 'success', "data" =>
            [
                "user" => $this->user,
                'permissions' => $permissions,
                "chat" => $chat,
                "percentages" => $percentages,

            ]
        ];

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
