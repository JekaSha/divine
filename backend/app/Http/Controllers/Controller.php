<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected string $identifier;
    protected string $lang;
    protected ?array $user = null;

    /**
     * Controller constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->identifier = $this->getIdentifier($request);
        $this->lang = $this->getUserLanguageInterface($request);
        $this->user = $this->getUserFromToken($request);

    }

    /**
     * Get identifier from the request.
     *
     * @param Request $request
     * @return string
     */
    protected function getIdentifier(Request $request): string
    {
        $identifier = $request->header('X-Identifier')
            ?? $request->input('identifier')
            ?? 1;

        return $identifier;
    }

    protected function getUserLanguageInterface(Request $request) :string {

        $lang = $request->header('User-Language-Interface')
            ?? $request->input('lang')
            ?? env('DEFAULT_LANGUAGE', 'en');

        return $lang;

    }

    /**
     * Get user data from the token.
     *
     * @param Request $request
     * @return array|null
     */
    /**
     * Get user data from the token.
     *
     * @param Request $request
     * @return array|null
     */
    protected function getUserFromToken(Request $request): ?array
    {

        // Use the bearerToken() method to get the token
        $token = $request->bearerToken() ?? $request->input('token');

        if (!$token) {
            return null; // No token provided
        }

        // Query the database for a user with the provided token
        $user = User::where('remember_token', $token)->first();

        return $user ? $user->toArray() : null; // Return user data as an array or null if not found
    }


}
