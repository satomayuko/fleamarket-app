<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'recommend');
        $keyword = $this->resolveKeyword($request);
        $catId = (int) $request->query('category_id', 0);
        $categoryIds = $this->resolveCategoryIds($catId);

        $items = $tab === 'mylist' && Auth::check()
            ? $this->mylistQuery($keyword, $categoryIds)->paginate(12)->withQueryString()
            : $this->itemsQuery($keyword, $categoryIds)->paginate(12)->withQueryString();

        return view('index', [
            'items' => $items,
            'active' => $tab,
            'keyword' => $keyword,
        ]);
    }

    public function indexMyList(Request $request): View
    {
        if (! Auth::check()) {
            abort(403);
        }

        $keyword = $this->resolveKeyword($request);
        $catId = (int) $request->query('category_id', 0);
        $categoryIds = $this->resolveCategoryIds($catId);

        $items = $this->mylistQuery($keyword, $categoryIds)
            ->paginate(12)
            ->withQueryString();

        return view('index', [
            'items' => $items,
            'active' => 'mylist',
            'keyword' => $keyword,
        ]);
    }

    public function show(Item $item): View
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

    public function create(): View
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('id')
            ->get();

        return view('items.create', ['categories' => $categories]);
    }

    public function store(ExhibitionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $path = $request->file('image')->store('items', 'public');

        $item = Item::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'image' => $path,
            'brand' => $validated['brand'] ?? null,
            'condition' => $validated['condition'],
            'shipping_fee_burden' => 'seller',
            'status' => 'selling',
            'published_at' => now(),
        ]);

        $item->categories()->sync($validated['categories']);

        return redirect()
            ->route('items.show', $item)
            ->with('success', '商品を出品しました');
    }

    private function resolveKeyword(Request $request): string
    {
        if ($request->has('keyword')) {
            session(['search.keyword' => trim((string) $request->query('keyword'))]);
        }

        return trim((string) session('search.keyword', ''));
    }

    private function resolveCategoryIds(int $catId): array
    {
        if ($catId <= 0) {
            return [];
        }

        $cat = Category::with('children:id,parent_id')
            ->select('id', 'parent_id')
            ->find($catId);

        if (! $cat) {
            return [];
        }

        $ids = $cat->children->pluck('id')->all();
        $ids[] = $cat->id;

        return $ids;
    }

    private function mylistQuery(string $keyword, array $categoryIds)
    {
        return Auth::user()
            ->favoriteItems()
            ->with(['order', 'categories'])
            ->when($keyword !== '', fn ($q) => $q->where('items.name', 'like', "%{$keyword}%"))
            ->when($categoryIds !== [], fn ($q) => $q->whereHas('categories', fn ($w) => $w->whereIn('categories.id', $categoryIds)))
            ->latest('items.id');
    }

    private function itemsQuery(string $keyword, array $categoryIds)
    {
        return Item::query()
            ->with(['order', 'categories'])
            ->when(Auth::check(), fn ($q) => $q->where('user_id', '!=', Auth::id()))
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
            ->when($categoryIds !== [], fn ($q) => $q->whereHas('categories', fn ($w) => $w->whereIn('categories.id', $categoryIds)))
            ->latest('id');
    }
}
