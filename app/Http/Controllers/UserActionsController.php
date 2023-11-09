<?php

namespace App\Http\Controllers;

use App\Models\UsersActions;
use Illuminate\Http\Request;

class UserActionsController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = UsersActions::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $endDate = date('Y-m-d', strtotime($request->date_to . ' +1 day'));
            $query->whereBetween('action_time', [$request->date_from, $endDate]);
        }

        $userActions = $query->latest()->get();

        return response()->json(['data' => $userActions]);
    }
}
