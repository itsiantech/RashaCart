<?php

namespace App\Livewire\Frontend\Components;

use Livewire\Component;
use App\Models\Product;

class SearchBar extends Component
{
    public $q = '';
    
    public function render()
    {
        $results = [];

        if(strlen($this->q) > 1){
            $results = Product::where('name', 'LIKE', '%'.$this->q.'%')->take(10)->get();
        }

        return view('livewire.frontend.components.search-bar', [
            'results' => $results,
            'q' => $this->q,
        ]);

    }
}
