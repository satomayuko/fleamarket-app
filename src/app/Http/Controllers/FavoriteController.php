<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class FavoriteController extends Controller
{
    public function store(Item $item)
    {
        Auth::user()
            ->favoriteItems()
            ->syncWithoutDetaching([$item->id]);

        return back()->with('success', 'いいねしました');
    }

    public function destroy(Item $item)
    {
        Auth::user()
            ->favoriteItems()
            ->detach($item->id);

        return back()->with('success', 'いいねを解除しました');
    }
}