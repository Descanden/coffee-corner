<?php

namespace App\Http\Controllers\Api;

use App\Models\CoffeeShop;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoffeeShopResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoffeeShopController extends Controller
{
    /**
     * Menampilkan daftar coffee shops dengan paginasi.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data coffee shop dengan paginasi 5 data per halaman
        $coffeeShops = CoffeeShop::latest()->paginate(5);

        // Menggunakan CoffeeShopResource untuk merapikan response
        return new CoffeeShopResource(true, 'Data Coffee Shops retrieved successfully', $coffeeShops);
    }

    /**
     * Menampilkan coffee shop berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Mencari coffee shop berdasarkan ID
        $coffeeShop = CoffeeShop::findOrFail($id);

        // Menggunakan CoffeeShopResource untuk merapikan response
        return new CoffeeShopResource(true, 'Coffee Shop retrieved successfully', $coffeeShop);
    }

    /**
     * Menambahkan coffee shop baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Menentukan aturan validasi
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'location'  => 'required|string|max:255',
            'owner'     => 'required|string|max:255',
            'rating'    => 'required|numeric|min:1|max:5',
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Membuat coffee shop baru
        $coffeeShop = CoffeeShop::create([
            'name'      => $request->name,
            'location'  => $request->location,
            'owner'     => $request->owner,
            'rating'    => $request->rating,
        ]);

        // Menggunakan CoffeeShopResource untuk merapikan response
        return new CoffeeShopResource(true, 'Coffee Shop Berhasil Ditambahkan!', $coffeeShop);
    }

    /**
     * Mengupdate coffee shop berdasarkan ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Menentukan aturan validasi
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'location'  => 'required|string|max:255',
            'owner'     => 'required|string|max:255',
            'rating'    => 'required|numeric|min:1|max:5',
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mencari coffee shop berdasarkan ID
        $coffeeShop = CoffeeShop::findOrFail($id);

        // Mengupdate data coffee shop
        $coffeeShop->update([
            'name'      => $request->name,
            'location'  => $request->location,
            'owner'     => $request->owner,
            'rating'    => $request->rating,
        ]);

        // Menggunakan CoffeeShopResource untuk merapikan response
        return new CoffeeShopResource(true, 'Data Coffee Shop Berhasil Diubah!', $coffeeShop);
    }

    /**
     * Menghapus coffee shop berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Mencari coffee shop berdasarkan ID
        $coffeeShop = CoffeeShop::findOrFail($id);

        // Menghapus coffee shop
        $coffeeShop->delete();

        // Menggunakan CoffeeShopResource untuk merapikan response
        return new CoffeeShopResource(true, 'Data Coffee Shop Berhasil Dihapus!', null);
    }
}