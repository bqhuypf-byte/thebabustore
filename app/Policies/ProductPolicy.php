<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool { return $user->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')); }
    public function view(User $user, Product $product): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, Product $product): bool { return $this->viewAny($user); }
    public function delete(User $user, Product $product): bool
    {
        return $this->viewAny($user) && !\App\Models\OrderItem::whereIn('product_variant_id', $product->variants()->select('id'))->exists();
    }
    public function deleteAny(User $user): bool { return $this->viewAny($user); }
}
