<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserInfoResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpStatus;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    /**
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return Response::json(['errors' => $validator->errors()], HttpStatus::HTTP_UNPROCESSABLE_ENTITY);
        }

        JWTAuth::factory()->setTTL(360000);

        if (!$token = Auth::attempt($validator->validated())) {
            return Response::json(
                ['status' => false, 'message' => 'Invalid credentials.'],
                HttpStatus::HTTP_UNAUTHORIZED);
        }

        return $this->createNewToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return Response::json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(Auth::refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return UserInfoResource
     */
    public function userProfile(): UserInfoResource
    {
        return new UserInfoResource(
            User::query()
                ->select('users.*', 'congregations.name as congregation_name')
                ->where('users.id', Auth::id())
                ->join('congregations', 'users.congregation_id', '=', 'congregations.id')
                ->first()
            );
    }

    public function userPermissions(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        return Response::json(['data' => $user->getAllPermissions()->pluck('name')]);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function createNewToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 360000,
            'user' => Auth::user()
        ]);
    }
}
