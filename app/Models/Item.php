<?php

namespace App\Models;

use App\Models\Category;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function itemCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transactions()
    {
        return $this->belongsToMany(User::class, 'transactions')
        ->withTimestamps()
        ->withPivot('sales');
    }
}
