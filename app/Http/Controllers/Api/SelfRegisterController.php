<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SelfRegisterRequest;
use App\Models\Congregation;
use Illuminate\Http\Request;

class SelfRegisterController extends Controller
{
    public function store(SelfRegisterRequest $request)
    {
        $newCongregation = Congregation::query()->create([
            'name' => $request->validated('congregation_name'),
            'location' => $request->validated('congregation_location'),
        ]);


    }
}
