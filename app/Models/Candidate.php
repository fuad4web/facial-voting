<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'party', 'bio', 'photo'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
