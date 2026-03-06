<?php
namespace Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    protected $table = 'forum_postings';
    protected $primaryKey = 'posting_id';
    protected $keyType = 'string';
    public $incrementing = false;

    public const CREATED_AT = 'mkdate';
    public const UPDATED_AT = 'chdate';


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
