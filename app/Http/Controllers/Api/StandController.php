<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Congregation;
use App\Models\Stand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StandController extends Controller
{
    public function index(Request $request): JsonResource
    {
        $validator = Validator::make($request->all(), [
            'congregation_id' => [
                'required',
                Rule::exists(Congregation::TABLE, 'id'),
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResource($validator->getMessageBag()); // or move to form request
        }

        $congregationId = $request->get('congregation_id');

        return new JsonResource([
            'data' => Stand::query()->where('congregation_id', $congregationId)->get()
        ]);
    }
}
