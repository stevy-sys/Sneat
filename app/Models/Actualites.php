<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actualites extends Model
{
    use HasFactory;
    protected $guarded = [] ;

    public function actualable()
    {
        return $this->morphTo();
    }
}
