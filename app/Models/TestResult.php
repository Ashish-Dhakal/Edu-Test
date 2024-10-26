<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    protected $guarded = [];

    // testresullt belong to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
