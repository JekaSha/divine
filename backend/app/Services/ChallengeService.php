<?php

namespace App\Services;

use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use App\Repositories\AiRepository;
use App\Repositories\PromptRepository;
use App\Repositories\PackageRepository;
use App\Models\Prompt;

class ChallengeService
{
    protected $userId = false;
    protected ChallengeRepositoryInterface $challengeRepository;
    protected AiRepository $aiRepository;
    protected PromptRepository $promptRepository;
    protected PackageRepository $packageRepository;

    public function __construct(
        ChallengeRepositoryInterface $challengeRepository,
        AiRepository $aiRepository,
        PromptRepository $promptRepository,
        PackageRepository $packageRepository
    )
    {
        $this->challengeRepository = $challengeRepository;
        $this->aiRepository = $aiRepository;
        $this->promptRepository = $promptRepository;
        $this->packageRepository = $packageRepository;
    }

    public function get(string $identifier)
    {
        $challenges =  $this->challengeRepository->get($identifier, $this->userId);

        return $challenges;
    }

    public function request(string $identifier, array $data): array
    {

        $prompt = $this->promptRepository->get(['id' => $data['prompt_id']])->first();
        if (!$prompt) {
            throw new \Exception('Prompt not found.');
        }

        $data['stream'] = is_array(@$data['stream']) ? $data['stream'] : [];

        $challengeData = [

            'guest_hash' => $identifier,
            'session_hash' => $data['session_hash'],
            'prompt_id' => $prompt->id,
            'request' => $data['request'],
            'prompt' => $this->promptRepository->render($prompt->template, $data['request'], ),
            'stream' => $data['stream'] ?? [],
        ];


        $challenge = $this->challengeRepository->store($identifier, $challengeData);

        if (!$challenge->response) {

            $time_start = time();
            $aiResponse = $this->aiRepository->sendRequest(
                $challengeData['prompt'],
                $prompt->ai_model,
                $prompt->ai_type
            );
            $time_end = time();
            $time = $time_end - $time_start;

            $answer = $aiResponse['choices'][0]['message']['content'];

            $challenge->update([
                'response' => $answer ?? null,
                'response_time' => $time ?? null,
            ]);
        } else {
            $answer = $challenge->response;
        }

        $r = [
            'status' => "success",
            "data" => [
                'request' => $challenge->request,
                'response' => $challenge->response,
            ]
        ];

        return $r;
    }

    public function createForUser(int $userId, array $data)
    {
        // Добавить идентификатор пользователя к данным
        $data['user_id'] = $userId;

        // Создать запрос
        return $this->challengeRepository->create($data);
    }

    public function updateForUser(int $userId, int $id, array $data)
    {
        // Получить запрос по ID и убедиться, что он принадлежит пользователю
        $challenge = $this->challengeRepository->getById($id);

        if (!$challenge || $challenge->user_id !== $userId) {
            return null;
        }

        // Обновить запрос
        return $this->challengeRepository->update($id, $data);
    }

    public function deleteForUser(int $userId, int $id)
    {
        // Получить запрос по ID и убедиться, что он принадлежит пользователю
        $challenge = $this->challengeRepository->getById($id);

        if (!$challenge || $challenge->user_id !== $userId) {
            return false;
        }

        // Удалить запрос
        return $this->challengeRepository->delete($id);
    }

    public function getPackages($data) {
        $packages = $this->packageRepository->get($data);
        return $packages;
    }
}
