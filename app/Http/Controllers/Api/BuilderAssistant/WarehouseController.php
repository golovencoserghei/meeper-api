<?php

namespace App\Http\Controllers\Api\BuilderAssistant;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemsToWarehouseRequest;
use App\Models\WarehouseItem;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouseItems = WarehouseItem::query()->paginate(30);

        if($request->ajax()){
            $warehouseItems = WarehouseItem::query()
                ->when($request->input('search_term'), function($q) use ($request){
                    $q->where('name_ru', 'like', '%' . $request->input('search_term') . '%')
                        ->orWhere('name_md', 'like', '%' . $request->input('search_term') . '%');
                })
                ->paginate(3);

            return response()->json(['data' => $warehouseItems]);
        }

        return response()->json(['data' => $warehouseItems]);
    }

    public function store(StoreItemsToWarehouseRequest $request)
    {
        $attributesToStore = [];

        if ($request->has('item_code')) {
            $attributesToStore['item_code'] = $request->input('item_code');
        }

        if ($request->has('name_ru')) {
            $attributesToStore['name_ru'] = $request->input('name_ru');
        }

        if ($request->has('name_md')) {
            $attributesToStore['name_md'] = $request->input('name_md');
        }

        if ($request->has('notes')) {
            $attributesToStore['name_md'] = $request->input('name_md');
        }

        $stored = WarehouseItem::query()->create($attributesToStore);

        return response()->json(['data' => $stored]);
    }
}
