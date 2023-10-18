<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class StandController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id'),
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResponse($validator->getMessageBag(), Response::HTTP_UNPROCESSABLE_ENTITY); // or move to form request
        }

        $congregationId = $request->get('congregation_id');

        return new JsonResponse([
            Stand::query()->where('congregation_id', $congregationId)->get()
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id'),
            ],
            'location' => [
                'required',
                'string',
                'max:255'
            ],
            'name' => [
                'required',
                'string',
                'max:255'
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(
                ['message' => $validator->getMessageBag()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            ); // or move to form request
        }

        $stand = Stand::query()->create($validator->validated());

        return new JsonResponse([
            'data' => $stand,
        ], Response::HTTP_CREATED);
    }

    public function show(int $id): JsonResponse
    {
        return new JsonResponse(['data' => Stand::query()->findOrFail($id)]);
    }

    /**
     * @throws ValidationException
     */
    public function update(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'location' => [
                'sometimes',
                'string',
                'max:255'
            ],
            'name' => [
                'sometimes',
                'string',
                'max:255'
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(
                ['message' => $validator->getMessageBag()],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var Stand $stand */
        $stand = Stand::query()->findOrFail($id);

        if ($request->input('location')) {
            $stand->location = $request->input('location');
        }

        if ($request->input('name')) {
            $stand->name = $request->input('name');
        }

        $stand->save();

        return new JsonResponse(['data' => $stand->refresh()], Response::HTTP_ACCEPTED);
    }

    public function destroy(int $id): JsonResponse
    {
        Stand::destroy($id);

        return new JsonResponse(status: Response::HTTP_NO_CONTENT);
    }
}
