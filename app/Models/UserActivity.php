<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserActivity extends Model
{
    use HasFactory;

    protected $table = 'user_activity';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'point',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
