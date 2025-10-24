<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function edit(Item $item)
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }
        if ($item->isSold()) {
            abort(410);
        }

        $saved = DB::table('item_addresses')
            ->where('item_id', $item->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($saved) {
            $zip = (string)($saved->zip ?? '');
            $address = (string)($saved->address ?? '');
            $building = (string)($saved->building ?? '');
        } else {
            $user = Auth::user();
            $zip = $this->firstFilled($user, ['zip', 'zipcode', 'postal_code', 'postcode']);
            $address = $this->joinFilled(' ', [
                $this->firstFilled($user, ['prefecture']),
                $this->firstFilled($user, ['city']),
                $this->firstFilled($user, ['street']),
                $this->firstFilled($user, ['address', 'address1', 'address_line1']),
            ]);
            $building = $this->firstFilled($user, ['building', 'address2', 'address_line2']);
        }

        return view('orders.address', [
            'item'     => $item,
            'zip'      => $zip,
            'address'  => $address,
            'building' => $building,
        ]);
    }

    public function update(AddressRequest $request, Item $item)
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }
        if ($item->isSold()) {
            abort(410);
        }

        $now = now();

        DB::table('item_addresses')->upsert(
            [[
                'item_id'    => $item->id,
                'user_id'    => Auth::id(),
                'zip'        => $request->zip,
                'address'    => $request->address,
                'building'   => $request->building,
                'created_at' => $now,
                'updated_at' => $now,
            ]],
            ['item_id', 'user_id'],
            ['zip', 'address', 'building', 'updated_at']
        );

        return redirect()->route('orders.confirm', $item)
            ->with('status', '配送先を更新しました');
    }

    private function firstFilled($model, array $keys): string
    {
        foreach ($keys as $key) {
            if (isset($model->{$key}) && $model->{$key} !== '') {
                return (string)$model->{$key};
            }
        }
        return '';
    }

    private function joinFilled(string $glue, array $parts): string
    {
        return implode($glue, array_values(array_filter($parts, fn($v) => $v !== '' && $v !== null)));
    }
}