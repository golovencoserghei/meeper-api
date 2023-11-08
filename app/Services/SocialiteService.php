<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Two\User as SocialiteUser;

class SocialiteService
{
    /**
     * Login or create user with incoming socialite provider.
     *
     * @throws Exception
     */
    public static function handleLogin(SocialiteUser $providerUser, string $provider)
    {
        switch ($provider) {
            case 'facebook':
                $first_name = $providerUser->offsetGet('first_name');
                $last_name = $providerUser->offsetGet('last_name');
                break;

            case 'google':
                $first_name = $providerUser->offsetGet('given_name');
                $last_name = $providerUser->offsetGet('family_name');
                break;
            default:
                $first_name = null;
                $last_name = null;
        }

        /** @var Authenticatable $registeredUser */
        $registeredUser = User::query()->where('email', $providerUser->email)->first();

        return DB::transaction(static function () use ($providerUser, $registeredUser, $last_name, $first_name) {
            $socialAccount = SocialAccount::query()->firstOrNew([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'google'
            ]);

            if (!$socialAccount->user) {
                /** @var Authenticatable $registeredUser */
                $registeredUser = User::query()->create([
                    'email' => $providerUser->getEmail(),
                    'name' => $first_name,
                    'surname' => $last_name
                ]);
                $socialAccount->fill(['user_id' => $registeredUser->id])->save();
            }

            auth()->login($registeredUser);

            return $registeredUser;
        });
    }
}
