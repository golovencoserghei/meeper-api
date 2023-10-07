<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\RolesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SelfRegisterRequest;
use App\Models\Congregation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

class RegistrationController extends Controller
{
    public function selfRegister(SelfRegisterRequest $request): JsonResponse
    {
        /** @var Congregation $newCongregation */
        $newCongregation = Congregation::query()->create([
            'name' => $request->validated('congregation_name'),
        ]);

        /** @var User $user */
        $user = User::query()->create([
            'first_name' => $request->validated('first_name'),
            'last_name' => $request->validated('last_name'),
            'email' => $request->validated('email'),
            'phone_number' => $request->validated('phone_number'),
            'congregation_id' => $newCongregation->id,
            'password' => bcrypt($request->password)
        ]);

        $user->assignRole(RolesEnum::RESPONSIBLE_FOR_STAND->value);

        return Response::json([
            'message' => 'User successfully registered',
            'user' => $user
        ], HttpStatus::HTTP_CREATED);
    }

    /**
     * Register a User.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return Response::json($validator->errors()->toJson(), HttpStatus::HTTP_BAD_REQUEST);
        }

        $user = User::query()->create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        $user->code = strtoupper(substr(md5($user->id), 0, 6)); // create unique user code
        $user->save();

        return Response::json([
            'message' => 'User successfully registered',
            'user' => $user
        ], HttpStatus::HTTP_CREATED);
    }
}
