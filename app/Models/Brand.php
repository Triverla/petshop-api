<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    use HasUuids;

    public $incrementing = false;
    protected $primaryKey = 'uuid';

    protected $fillable = [
        'title',
        'slug',
    ];
}
