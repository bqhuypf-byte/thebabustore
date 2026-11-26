<?php
namespace App\Policies;
use App\Models\PromotionCampaign;
use App\Models\User;
class PromotionCampaignPolicy {
    public function viewAny(User $user): bool { return $user->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')); }
    public function view(User $user,PromotionCampaign $campaign): bool { return $this->viewAny($user); }
    public function create(User $user): bool { return $this->viewAny($user); }
    public function update(User $user,PromotionCampaign $campaign): bool { return $this->viewAny($user); }
    public function delete(User $user,PromotionCampaign $campaign): bool { return $this->viewAny($user); }
    public function deleteAny(User $user): bool { return $this->viewAny($user); }
}
