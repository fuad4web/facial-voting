<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name', 'voter_id', 'email', 'password', 'facial_descriptors', 'facial_image', 'has_voted', 'last_vote_at', 'is_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'facial_descriptors',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // mutator to encrypt facial description whe  saving
    public function setFacialDescriptorsAttribute($value)
    {
        $this->attributes['facial_descriptors'] = $value ? Crypt::encryptString($value) : null;
    }

    // for accessing it fro db
    public function getFacialDescriptorsAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (DecryptException $e) {
            return null;
        }
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    // Helper to check if user has voted in a specific category
    public function hasVotedInCategory($categoryId)
    {
        return $this->votes()->where('category_id', $categoryId)->exists();
    }
}
