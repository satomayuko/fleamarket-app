<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class OrderController extends Controller
{
    public function confirm(Item $item)
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        if ($item->isSold()) {
            abort(410);
        }

        $user = Auth::user();

        $defaultAddress = [
            'zip' => $this->firstFilled($user, ['zip', 'zipcode', 'postal_code', 'postcode']),
            'line' => $this->joinFilled(' ', [
                $this->firstFilled($user, ['prefecture']),
                $this->firstFilled($user, ['city']),
                $this->firstFilled($user, ['street']),
                $this->firstFilled($user, ['address', 'address1', 'address_line1']),
                $this->firstFilled($user, ['building', 'address2', 'address_line2']),
            ]),
        ];

        $overridden = DB::table('item_addresses')
            ->where('item_id', $item->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($overridden) {
            $line = trim((string)($overridden->address ?? '') . ' ' . (string)($overridden->building ?? ''));
            $defaultAddress = [
                'zip'  => (string)($overridden->zip ?? ''),
                'line' => trim($line),
            ];
        }

        return view('orders.confirm', [
            'item' => $item,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function checkout(Item $item, Request $request)
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        if ($item->isSold()) {
            abort(410);
        }

        $data = $request->validate([
            'payment_method' => ['required', 'in:convenience,card'],
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => [
                $data['payment_method'] === 'convenience' ? 'konbini' : 'card',
            ],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $item->name],
                    'unit_amount' => (int)$item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('orders.success', ['item' => $item->id]),
            'cancel_url' => route('orders.confirm', $item),
        ]);

        return redirect($session->url);
    }

    public function success(Item $item)
    {
        if (!$item->isSold()) {
            $item->update(['status' => 'sold']);

            $address = DB::table('item_addresses')
                ->where('user_id', Auth::id())
                ->orderByDesc('id')
                ->first();

            $shippingAddressId = $address->id ?? null;

            Order::updateOrCreate(
                ['item_id' => $item->id],
                [
                    'buyer_id' => Auth::id(),
                    'shipping_address_id' => $shippingAddressId,
                    'price_at_purchase' => $item->price,
                    'status' => 'paid',
                ]
            );
        }

        return redirect()->route('items.index')->with('status', '購入が完了しました。');
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