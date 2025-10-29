<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest;
use App\Models\Order;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile;

        $sellingItems = $user->items()
            ->latest('id')
            ->get(['id', 'name as title', 'image as cover_image']);

        $purchasedItems = Order::where('buyer_id', $user->id)
            ->with([
                'item' => function ($q) {
                    $q->select('id', 'name as title', 'image as cover_image');
                }
            ])
            ->latest('id')
            ->get()
            ->pluck('item')
            ->filter()
            ->values();

        return view('profile', compact('user', 'profile', 'sellingItems', 'purchasedItems'));
    }

    public function edit()
    {
        $user = Auth::user();

        $profile = $user->profile ?: $user->profile()->create([
            'postal_code' => '',
            'address' => '',
            'building' => '',
            'avatar_path' => null,
        ]);

        return view('profile_edit', compact('profile', 'user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->update([
            'name' => $request->name,
        ]);

        $profile = $user->profile ?: $user->profile()->create();

        $avatarPath = $profile->avatar_path;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('profiles', 'public');
        }

        $profile->update([
            'postal_code' => $request->postal,
            'address' => $request->address,
            'building' => $request->building,
            'avatar_path' => $avatarPath,
        ]);

        return redirect()
            ->route('mypage')
            ->with('status', 'プロフィールを更新しました！');
    }
}