<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function store(Item $item): RedirectResponse
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        Auth::user()
            ->favoriteItems()
            ->syncWithoutDetaching([$item->id]);

        return back()->with('success', 'いいねしました');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        Auth::user()
            ->favoriteItems()
            ->detach($item->id);

        return back()->with('success', 'いいねを解除しました');
    }
}