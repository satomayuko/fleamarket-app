<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();

        $profile = $user->profile ?: $user->profile()->create([
            'postal_code' => '',
            'address' => '',
            'building' => '',
            'avatar_path' => null,
        ]);

        $sellingItems = $user->items()
            ->latest('id')
            ->get(['id', 'name as title', 'image as cover_image']);

        $purchasedItems = Order::where('buyer_id', $user->id)
            ->with([
                'item' => function ($q) {
                    $q->select('id', 'name as title', 'image as cover_image');
                },
            ])
            ->latest('id')
            ->get()
            ->pluck('item')
            ->filter()
            ->unique('id')
            ->values();

        return view('profile', [
            'user' => $user,
            'profile' => $profile,
            'sellingItems' => $sellingItems,
            'purchasedItems' => $purchasedItems,
        ]);
    }

    public function edit(): View
    {
        $user = Auth::user();

        $profile = $user->profile ?: $user->profile()->create([
            'postal_code' => '',
            'address' => '',
            'building' => '',
            'avatar_path' => null,
        ]);

        return view('profile_edit', [
            'profile' => $profile,
            'user' => $user,
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'name' => (string) $request->name,
        ]);

        $profile = $user->profile ?: $user->profile()->create();
        $avatarPath = $profile->avatar_path;

        if ($request->hasFile('avatar')) {
            if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
            $avatarPath = $request->file('avatar')->store('profiles', 'public');
        }

        $profile->update([
            'postal_code' => (string) ($request->postal ?? ''),
            'address' => (string) ($request->address ?? ''),
            'building' => (string) ($request->building ?? ''),
            'avatar_path' => $avatarPath,
        ]);

        return redirect()
            ->route('mypage')
            ->with('status', 'プロフィールを更新しました！');
    }
}
