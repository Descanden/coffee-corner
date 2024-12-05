<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Menampilkan daftar posts dengan paginasi.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Mengambil semua data post dengan paginasi 5 data per halaman
        $posts = Post::latest()->paginate(5);

        // Menggunakan PostResource untuk merapikan response
        return new PostResource(true, 'Data posts retrieved successfully', $posts);
    }

    /**
     * Menampilkan post berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Mencari post berdasarkan ID
        $post = Post::findOrFail($id);

        // Menggunakan PostResource untuk merapikan response
        return new PostResource(true, 'Post retrieved successfully', $post);
    }

    /**
     * Menambahkan post baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Menentukan aturan validasi
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
            'author'    => 'required',
            'category'  => 'required',
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Menyimpan gambar
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // Membuat post baru
        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
            'author'    => $request->author,
            'category'  => $request->category,
        ]);

        // Menggunakan PostResource untuk merapikan response
        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * Mengupdate post berdasarkan ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Menentukan aturan validasi
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
            'author'    => 'required',
            'category'  => 'required',
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mencari post berdasarkan ID
        $post = Post::findOrFail($id);

        // Cek apakah ada gambar baru
        if ($request->hasFile('image')) {
            // Menyimpan gambar
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            // Menghapus gambar lama
            Storage::delete('public/posts/' . basename($post->image));

            // Mengupdate post dengan gambar baru
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'category'  => $request->category,
            ]);
        } else {
            // Mengupdate post tanpa gambar
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'category'  => $request->category,
            ]);
        }

        // Menggunakan PostResource untuk merapikan response
        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    /**
     * Menghapus post berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        // Mencari post berdasarkan ID
        $post = Post::findOrFail($id);

        // Menghapus gambar
        Storage::delete('public/posts/' . basename($post->image));

        // Menghapus post
        $post->delete();

        // Menggunakan PostResource untuk merapikan response
        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}