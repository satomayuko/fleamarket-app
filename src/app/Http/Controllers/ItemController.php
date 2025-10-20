<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = trim((string) $request->query('keyword'));

        if ($tab === 'mylist' && Auth::check()) {
            $items = Auth::user()
                ->favoriteItems()
                ->with('order')
                ->when($keyword !== '', function ($q) use ($keyword) {
                    $q->where('items.name', 'like', "%{$keyword}%");
                })
                ->latest('items.id')
                ->paginate(12)
                ->withQueryString();
        } else {
            $items = Item::query()
                ->with('order')
                ->when(Auth::check(), function ($q) {
                    $q->where('user_id', '!=', Auth::id());
                })
                ->when($keyword !== '', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                })
                ->latest('id')
                ->paginate(12)
                ->withQueryString();
        }

        return view('index', [
            'items'   => $items,
            'active'  => $tab,
            'keyword' => $keyword,
        ]);
    }

    public function show(Item $item)
    {
        $item->load([
            'categories.parent',
            'user',
            'comments.user',
            'order',
        ])->loadCount([
            'favoredByUsers as favorites_count',
            'comments',
        ]);

        return view('items.show', compact('item'));
    }

    public function create()
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('id')
            ->get();

        return view('items.create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string', 'max:255'],
            'price'        => ['required', 'integer', 'min:1'],
            'image'        => ['required', 'image', 'mimes:jpeg,jpg,png'],
            'brand'        => ['nullable', 'string', 'max:255'],
            'condition'    => ['required', 'in:新品,未使用に近い,目立った傷や汚れなし,やや傷や汚れあり,傷や汚れあり'],
            'categories'   => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ]);

        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id'             => Auth::id(),
            'name'                => $request->name,
            'description'         => $request->description,
            'price'               => $request->price,
            'image'               => $path,
            'brand'               => $request->brand,
            'condition'           => $request->condition,
            'shipping_fee_burden' => 'seller',
            'status'              => 'selling',
            'published_at'        => now(),
        ]);

        $item->categories()->sync($request->input('categories'));

        return redirect()
            ->route('items.show', $item)
            ->with('success', '商品を出品しました');
    }
}