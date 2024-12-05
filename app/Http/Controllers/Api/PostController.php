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
    public function index()
    {
        $posts = Post::latest()->paginate(5);

        $pagination = [
            'current_page' => $posts->currentPage(),
            'total_pages' => $posts->lastPage(),
            'per_page' => $posts->perPage(),
            'total_items' => $posts->total(),
            'next_page_url' => $posts->nextPageUrl(),
            'prev_page_url' => $posts->previousPageUrl(),
            'first_page_url' => $posts->url(1),
            'last_page_url' => $posts->url($posts->lastPage())
        ];

        return new PostResource(true, 'Data posts retrieved successfully', [
            'posts' => $posts->items(),
            'pagination' => $pagination
        ]);
    }

    public function show($id)
    {
        $perPage = 5;
        $totalItems = Post::count();
        $totalPages = ceil($totalItems / $perPage);

        $post = Post::findOrFail($id);

        $currentPage = ceil(($id / $perPage));

        $nextPageUrl = ($currentPage < $totalPages) ? url('/api/posts?page=' . ($currentPage + 1)) : null;
        $prevPageUrl = ($currentPage > 1) ? url('/api/posts?page=' . ($currentPage - 1)) : null;
        $firstPageUrl = url('/api/posts?page=1');
        $lastPageUrl = ($totalPages > 1) ? url('/api/posts?page=' . $totalPages) : null;

        return new PostResource(true, 'Post retrieved successfully', [
            'posts' => [$post],
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
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title'     => 'required',
            'content'   => 'required',
            'author'    => 'required',
            'category'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        $post = Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
            'author'    => $request->author,
            'category'  => $request->category,
        ]);

        return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
            'author'    => 'required',
            'category'  => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::findOrFail($id);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            Storage::delete('public/posts/' . basename($post->image));

            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'category'  => $request->category,
            ]);
        } else {
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content,
                'author'    => $request->author,
                'category'  => $request->category,
            ]);
        }

        return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        Storage::delete('public/posts/' . basename($post->image));

        $post->delete();

        return new PostResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}