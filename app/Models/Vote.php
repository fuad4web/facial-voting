<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::created(function ($vote) {
            $user = $vote->user;
            if (!$user->has_voted) {
                $user->has_voted = true;
                $user->last_vote_at = now();
                $user->save();
            }
        });
    }

    public $timestamps = false; // we use voted_at instead of created_at/updated_at

    protected $fillable = [
        'user_id', 'candidate_id', 'category_id', 'voted_at'
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
