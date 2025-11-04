<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function confirm(Item $item): View
    {
        $this->assertPurchasable($item);

        $overridden = DB::table('addresses')
            ->where('user_id', Auth::id())
            ->latest('id')
            ->first();

        if ($overridden) {
            $defaultAddress = [
                'zip' => (string)($overridden->zip ?? ''),
                'line' => trim((string)($overridden->address ?? '') . ' ' . (string)($overridden->building ?? '')),
            ];
        } else {
            $profile = DB::table('profiles')
                ->where('user_id', Auth::id())
                ->first();

            if ($profile) {
                $defaultAddress = [
                    'zip' => (string)($profile->postal_code ?? ''),
                    'line' => trim((string)($profile->address ?? '') . ' ' . (string)($profile->building ?? '')),
                ];
            } else {
                $user = Auth::user();
                $defaultAddress = [
                    'zip' => $this->firstFilled($user, ['zip', 'zipcode', 'postal', 'postal_code', 'postcode']),
                    'line' => $this->joinFilled(' ', [
                        $this->firstFilled($user, ['prefecture']),
                        $this->firstFilled($user, ['city']),
                        $this->firstFilled($user, ['street']),
                        $this->firstFilled($user, ['address', 'address1', 'address_line1']),
                        $this->firstFilled($user, ['building', 'address2', 'address_line2']),
                    ]),
                ];
            }
        }

        return view('orders.confirm', [
            'item' => $item,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    public function checkout(Item $item, PurchaseRequest $request): RedirectResponse
    {
        $this->assertPurchasable($item);

        $data = $request->validated();

        Stripe::setApiKey((string)config('services.stripe.secret'));

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

        return redirect((string)$session->url);
    }

    public function success(Item $item): RedirectResponse
    {
        if (!$item->isSold()) {
            $item->update(['status' => 'sold']);

            $addr = DB::table('addresses')
                ->where('user_id', Auth::id())
                ->latest('id')
                ->first();

            if ($addr) {
                $shipZip = (string)($addr->zip ?? '');
                $shipAddress = (string)($addr->address ?? '');
                $shipBuilding = $addr->building !== null ? (string)$addr->building : null;
            } else {
                $user = Auth::user();
                $shipZip = $this->firstFilled($user, ['zip', 'zipcode', 'postal_code', 'postcode']);
                $shipAddress = $this->joinFilled(' ', [
                    $this->firstFilled($user, ['prefecture']),
                    $this->firstFilled($user, ['city']),
                    $this->firstFilled($user, ['street']),
                    $this->firstFilled($user, ['address', 'address1', 'address_line1']),
                ]);
                $shipBuilding = $this->firstFilled($user, ['building', 'address2', 'address_line2']) ?: null;
            }

            Order::updateOrCreate(
                ['item_id' => $item->id],
                [
                    'buyer_id' => Auth::id(),
                    'price_at_purchase' => $item->price,
                    'status' => 'paid',
                    'ship_zip' => $shipZip,
                    'ship_address' => $shipAddress,
                    'ship_building' => $shipBuilding,
                ]
            );
        }

        return redirect()->route('items.index')->with('status', '購入が完了しました。');
    }

    private function assertPurchasable(Item $item): void
    {
        if ($item->user_id === Auth::id()) {
            abort(403);
        }

        if ($item->isSold()) {
            abort(410);
        }
    }

    private function firstFilled(object $model, array $keys): string
    {
        foreach ($keys as $key) {
            $v = $model->{$key} ?? '';
            if ($v !== '' && $v !== null) {
                return (string)$v;
            }
        }
        return '';
    }

    private function joinFilled(string $glue, array $parts): string
    {
        $filtered = array_values(array_filter($parts, fn($v) => $v !== '' && $v !== null));
        return implode($glue, $filtered);
    }
}