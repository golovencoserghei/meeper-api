<?php

namespace App\Http\Controllers\Api\Auth;

use App\Traits\InteractsWithFacebookGraphApi;
use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Services\SocialiteService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialAuthController extends Controller
{
    use InteractsWithFacebookGraphApi;

    /**
     * Login with facebook auth or google auth.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function socialiteLogin(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'access_token' => Rule::requiredIf($request->input('provider') === 'facebook'),
                'provider' => [
                    'required',
                    Rule::in(['google', 'facebook'])
                ]
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            switch ($request->input('provider')) {
                case 'facebook':
                    $providerUser =  $this->initFacebookLoginFlow(request('access_token'));
                    break;
                case 'google':
                    $providerUser = Socialite::driver('google')->stateless()->user();
                    break;
            }

            $user = SocialiteService::handleLogin($providerUser, $request->input('provider'));

            return response()->json([
                'access_token' => JWTAuth::fromUser($user),
                'expires_in' => 60 * 100000
            ]);

        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Login with google auth.
     *
     * @return JsonResponse
     */
    public function googleLogin(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'provider' => [
                    'required',
                    Rule::in(['google', 'facebook'])
                ],
                'name' => [
                    'required',
                    'string',
                    'regex:/^[a-z ,.\'-]+$/i'
                ],
                'surname' => [
                    'required',
                    'string',
                    'regex:/^[a-z ,.\'-]+$/i'
                ],
                'email' => [
                    'required',
                    'email'
                ],
                'social_id' => [
                    'required'
                ]
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = DB::transaction(function () use ($request) {
                $user = User::whereEmail($request->email)->first();

                if(isset($user->password)){
                    throw new Exception("This is rapidasig user, not socialite.");
                }

                $socialAccount = SocialAccount::firstOrNew([
                    'provider_user_id' => $request->social_id,
                    'provider' => $request->provider
                ]);

                if($socialAccount->user){
                    auth()->login($user);
                } else {
                    $user = User::create([
                        'email' => $request->email,
                        'name' =>  $request->name,
                        'surname' => $request->surname
                    ]);
                    $socialAccount->fill(['user_id' => $user->id])->save();
                    auth()->login($user);
                }

                return $user;
            });

            return response()->json([
                'status' => true,
                'access_token' => JWTAuth::setTTL(60 * 100000)->fromUser($user),
                'expires_in' => 60 * 100000
            ]);

        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'errors' => $e->errors()]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get a link for google auth page.
     *
     * @return JsonResponse
     */
    public function googleLoginUrl(): JsonResponse
    {
        return response()->json(['status' => true, 'url' => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()]);
    }
}
