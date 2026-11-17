<?php
namespace App\Livewire;
/** Compatibility component: all activation entry points use the verified workflow. */
class WarrantyActivation extends WarrantyLookup {
    public function mount(?string $code=null): void { parent::mount($code); $this->activeTab='activate'; }
}
