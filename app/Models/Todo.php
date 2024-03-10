<?php

namespace App\Models;

use App\Models\User;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @author Yakubu Alhassan <yaqoubdramani@gmail.com>
 */
class Todo extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = ['title', 'completed'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
