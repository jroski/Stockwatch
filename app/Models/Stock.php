<?php

namespace App\Models;

class Stock extends BaseModel
{

    //=== RELATIONSHIPS ===//
    public function owner()
    {
        $this->belongsTo(User::class);
    }

    public function houseguest()
    {
        $this->belongsTo(Houseguest::class);
    }
}
