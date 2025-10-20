<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Item;

class CommentController extends Controller
{
    // コメント投稿処理
    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'comment' => ['required', 'string', 'max:255'],
        ]);

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'body'    => $request->comment,
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'コメントを投稿しました');
    }
}