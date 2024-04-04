<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;


class NewsController extends Controller
{

    public function index()
    {
        $news = NewsResource::collection(News::with(["user"])->orderBy('created_at', 'desc')->paginate(3));
        return response()->json($news);
    }



    public function store(StoreNewsRequest $request)
    {
        $News = new News([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'user_id' => auth()->user()->id,
        ]);

        $this->authorize('create', $News);
        $News->save();

        return response()->json(new NewsResource($News), 201);
    }

    public function show(string $id)
    {
        $News = News::find($id);

        if (!$News) {
            return response()->json(['error' => 'Новость не найдена'], 404);
        }
        $NewsResource = new NewsResource($News);
        return response()->json($NewsResource);
    }

    public function update(UpdateNewsRequest $request, $id)
    {
        $user = auth()->user();
        $News = News::find($id);

        if (!$News) {
            return response()->json(['error' => 'Новости не найдены'], 404);
        }

        $this->authorize('update', $News);

        $News->update([
            'title' => $request->has('title') ? $request->input('title') : $user->title,
            'content' => $request->has('content') ? $request->input('content') : $user->content,
            'book_id' => $request->has('book_id') ? $request->input('book_id') : $user->book_id,
        ]);

        return new NewsResource($News);
    }

    public function destroy($id)
    {
        $News = News::find($id);

        if (!$News) {
            return response()->json(['error' => 'Новость не найдена'], 404);
        }
        $this->authorize('delete', $News);
        $News->delete();

        return response()->json(['message' => 'Новость успешно удалена']);
    }
}
