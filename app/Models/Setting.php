<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = [
        'user_id', 'lab_name', 'lab_address', 'lab_phone', 'lab_email',
        'header_image', 'footer_image', 'stamp_image', 'signature_image',
        'authorized_name', 'authorized_title',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
