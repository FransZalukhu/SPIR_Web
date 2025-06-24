<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'content',
    ];

    // Relasi: Comment dimiliki oleh 1 report
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    // Relasi: Comment dimiliki oleh 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
