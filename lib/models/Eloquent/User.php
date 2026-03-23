<?php
namespace Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $table = 'auth_user_md5';
    protected $primaryKey = 'user_id';
    protected $keyType = 'string';
    public $incrementing = false;

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }
}
