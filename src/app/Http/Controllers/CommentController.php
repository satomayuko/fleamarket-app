<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item): RedirectResponse
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        $data = $request->validated();

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'body' => $data['comment'],
        ]);

        return redirect()
            ->route('items.show', $item)
            ->with('success', 'コメントを投稿しました');
    }
}