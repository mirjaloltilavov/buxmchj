<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = ['id'];

    public const STATUSES = [
        'new' => 'Yangi',
        'contacted' => 'Bog‘lanildi',
        'done' => 'Yakunlandi',
        'spam' => 'Spam',
    ];

    public const INTERESTS = [
        'course' => 'O‘quv kursi',
        'service' => 'Buxgalteriya xizmati',
    ];
}
