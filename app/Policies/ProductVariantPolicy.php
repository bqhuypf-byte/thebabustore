<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;

class ProductVariantPolicy
{
    public function viewAny(User $user): bool { return $user->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')); }
    public function view(User $user, ProductVariant $variant): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user, ProductVariant $variant): bool { return $this->viewAny($user); }
    public function delete(User $user, ProductVariant $variant): bool
    {
        return $this->viewAny($user) && !\App\Models\OrderItem::where('product_variant_id', $variant->id)->exists();
    }
    public function deleteAny(User $user): bool { return $this->viewAny($user); }
}
