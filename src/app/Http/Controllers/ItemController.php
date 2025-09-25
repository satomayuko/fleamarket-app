<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->query('tab') === 'mylist' && Auth::check()) {
            $items = Auth::user()->favoriteItems()->get();
        } else {
            $items = Item::latest()->get();
        }

        return view('items.index', compact('items'));
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png',
        ]);

        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $path,
            'category_id' => $request->category_id,
            'condition' => $request->condition,
        ]);

        return redirect()->route('items.show', $item)->with('success', '商品を出品しました');
    }
}