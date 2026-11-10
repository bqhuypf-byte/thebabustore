<?php

namespace App\Helpers;

use App\Models\ProductVariant;

class CartManagement
{
    /**
     * Session-based cart storage.
     *
     * IMPORTANT:
     * - Using session avoids cookie size limits (and the "Set-Cookie too large" / 502 issue).
     * - We store only minimal payload in session: variant_id + quantity.
     * - On read, we "hydrate" items from DB for UI/checkout.
     */

    // Add item to the cart
    static public function addItemToCart($product_variant_id, $quantity)
    {
        // Validate variant exists (and allow us to fail fast for invalid IDs)
        $variantExists = ProductVariant::where('id', $product_variant_id)->exists();
        if (!$variantExists) {
            return count(self::getRawCartItemsFromSession());
        }

        $cart_items = self::getRawCartItemsFromSession();
        $existing_item = null;

        // Check if the item already exists in the cart
        foreach ($cart_items as $key => $item) {
            if ($item['product_variant_id'] == $product_variant_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity'] += $quantity;
        } else {
            // Store minimal payload in session
            $cart_items[] = [
                'product_variant_id' => (int) $product_variant_id,
                'quantity' => (int) $quantity,
            ];
        }

        // Save updated cart items to session
        self::saveRawCartItemsToSession($cart_items);
        return count($cart_items); // count of distinct items
    }

    static protected function sessionKey(): string
    {
        return 'cart_items';
    }

    static protected function saveRawCartItemsToSession(array $cart_items): void
    {
        session()->put(self::sessionKey(), array_values($cart_items));
    }

    /**
     * Backward-compat: if an old cookie cart exists, move it into session once.
     */
    static protected function migrateCookieCartToSessionIfNeeded(): void
    {
        if (session()->has(self::sessionKey())) {
            return;
        }

        $cookieRaw = request()->cookie(self::sessionKey());
        if (!$cookieRaw) {
            return;
        }

        $decoded = json_decode($cookieRaw, true);
        if (!is_array($decoded)) {
            return;
        }

        $normalized = array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) return null;
            if (!isset($item['product_variant_id'], $item['quantity'])) return null;
            $id = (int) $item['product_variant_id'];
            $qty = (int) $item['quantity'];
            if ($id <= 0 || $qty <= 0) return null;
            return ['product_variant_id' => $id, 'quantity' => $qty];
        }, $decoded)));

        if (!empty($normalized)) {
            session()->put(self::sessionKey(), $normalized);
        }

        // Clear old cookie (best-effort)
        cookie()->queue(cookie()->forget(self::sessionKey()));
    }

    // Remove cart item from session
    static public function removeCartItem($product_variant_id)
    {
        $cart_items = self::getRawCartItemsFromSession();
        foreach ($cart_items as $key => $item) {
            if ($item['product_variant_id'] == $product_variant_id) {
                unset($cart_items[$key]);
                break; // Exit loop after removing the item
            }
        }
        self::saveRawCartItemsToSession($cart_items); // Reindex happens in save
        return self::hydrateCartItems($cart_items);
    }

    // Clear cart items from session
    static public function clearCartItems()
    {
        session()->forget(self::sessionKey());
        // also clear legacy cookie if present
        cookie()->queue(cookie()->forget(self::sessionKey()));
    }

    // Get all raw cart items from session (minimal payload)
    static public function getRawCartItemsFromSession()
    {
        self::migrateCookieCartToSessionIfNeeded();

        $cart_items = session()->get(self::sessionKey(), []);
        if (!is_array($cart_items)) {
            return [];
        }
        // Normalize payload
        return array_values(array_filter(array_map(function ($item) {
            if (!is_array($item)) return null;
            if (!isset($item['product_variant_id'], $item['quantity'])) return null;
            $id = (int) $item['product_variant_id'];
            $qty = (int) $item['quantity'];
            if ($id <= 0 || $qty <= 0) return null;
            return [
                'product_variant_id' => $id,
                'quantity' => $qty,
            ];
        }, $cart_items)));
    }

    // Get hydrated cart items (full data) for UI/checkout calculations
    // Backward compatible name (used across components)
    static public function getCartItemsFromCookie()
    {
        $raw = self::getRawCartItemsFromSession();
        return self::hydrateCartItems($raw);
    }

    // Preferred name for clarity
    static public function getCartItemsFromSession()
    {
        return self::getCartItemsFromCookie();
    }

    static protected function hydrateCartItems(array $rawItems)
    {
        if (empty($rawItems)) return [];

        $ids = array_values(array_unique(array_column($rawItems, 'product_variant_id')));
        $variants = ProductVariant::with('product:id,name,slug,images')
            ->whereIn('id', $ids)
            ->get(['id', 'name', 'sku', 'price', 'discount_price', 'product_id', 'image', 'warranty_days'])
            ->keyBy('id');

        $hydrated = [];
        foreach ($rawItems as $raw) {
            $variant = $variants->get($raw['product_variant_id']);
            if (!$variant) {
                continue; // variant removed/unavailable
            }
            $qty = (int) $raw['quantity'];
            $unit = (float) $variant->price;
            $hydrated[] = [
                'product_variant_id' => (int) $variant->id,
                'name' => $variant->name,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'discount_price' => (float) $variant->discount_price,
                'product_id' => (int) $variant->product_id,
                'product_name' => $variant->product?->name,
                'product_slug' => $variant->product?->slug,
                'image' => $variant->image ?: ($variant->product?->images[0] ?? null),
                'quantity' => $qty,
                'unit_amount' => $unit,
                'total_amount' => $unit * $qty,
                'warranty_days' => (int) ($variant->warranty_days ?? 0),
            ];
        }

        return $hydrated;
    }

    // Increment item quantity
    static public function incrementQuantityToCartItem($product_variant_id)
    {
        $cart_items = self::getRawCartItemsFromSession();
        foreach ($cart_items as $key => $item) {
            if ($item['product_variant_id'] == $product_variant_id) {
                $cart_items[$key]['quantity']++;
                break; // Exit loop after updating the item
            }
        }
        self::saveRawCartItemsToSession($cart_items);
        return self::hydrateCartItems($cart_items);
    }

    // Decrement quantity
    static public function decrementQuantityToCartItem($product_variant_id)
    {
        $cart_items = self::getRawCartItemsFromSession();
        foreach ($cart_items as $key => $item) {
            if ($item['product_variant_id'] == $product_variant_id) {
                if ($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                } else {
                    // If quantity is 1, we might want to remove the item instead
                    unset($cart_items[$key]);
                }
                break; // Exit loop after updating the item
            }
        }
        $cart_items = array_values($cart_items);
        self::saveRawCartItemsToSession($cart_items); // Reindex the array
        return self::hydrateCartItems($cart_items);
    }

    // Calculate grand total
    static public function calculateGrandTotal($items)
    {
        return array_sum(array_column($items, 'total_amount'));
    }
}
