<?php

namespace App\Http\Controllers\Api;

use App\Models\CoffeeShop;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoffeeShopResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CoffeeShopController extends Controller
{
    public function index()
    {
        $coffeeShops = CoffeeShop::latest()->paginate(5);

        return new CoffeeShopResource(true, 'Data Coffee Shops retrieved successfully', $coffeeShops);
    }

    public function show($id)
    {
        $perPage = 5;
        $totalItems = CoffeeShop::count();
        $totalPages = ceil($totalItems / $perPage);

        $coffeeShop = CoffeeShop::findOrFail($id);

        $currentPage = ceil(($id / $perPage));

        $nextPageUrl = ($currentPage < $totalPages) ? url('/api/coffeeshops?page=' . ($currentPage + 1)) : null;
        $prevPageUrl = ($currentPage > 1) ? url('/api/coffeeshops?page=' . ($currentPage - 1)) : null;
        $firstPageUrl = url('/api/coffeeshops?page=1');
        $lastPageUrl = ($totalPages > 1) ? url('/api/coffeeshops?page=' . $totalPages) : null;

        return new CoffeeShopResource(true, 'Coffee Shop retrieved successfully', [
            'coffeeshops' => [$coffeeShop],
            'pagination' => [
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalItems,
                'next_page_url' => $nextPageUrl,
                'prev_page_url' => $prevPageUrl,
                'first_page_url' => $firstPageUrl,
                'last_page_url' => $lastPageUrl,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $coffeeShop = CoffeeShop::create([
            'name' => $request->name,
            'location' => $request->location,
            'owner' => $request->owner,
            'rating' => $request->rating,
        ]);

        return new CoffeeShopResource(true, 'Coffee Shop Berhasil Ditambahkan!', $coffeeShop);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'owner' => 'required|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $coffeeShop = CoffeeShop::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/coffeeshops', $image->hashName());
            Storage::delete('public/coffeeshops/' . basename($coffeeShop->image));

            $coffeeShop->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'location' => $request->location,
                'owner' => $request->owner,
                'rating' => $request->rating,
            ]);
        } else {
            $coffeeShop->update([
                'name' => $request->name,
                'location' => $request->location,
                'owner' => $request->owner,
                'rating' => $request->rating,
            ]);
        }

        return new CoffeeShopResource(true, 'Data Coffee Shop Berhasil Diubah!', $coffeeShop);
    }

    public function destroy($id)
    {
        $coffeeShop = CoffeeShop::findOrFail($id);

        Storage::delete('public/coffeeshops/' . basename($coffeeShop->image));

        $coffeeShop->delete();

        return new CoffeeShopResource(true, 'Data Coffee Shop Berhasil Dihapus!', null);
    }
}