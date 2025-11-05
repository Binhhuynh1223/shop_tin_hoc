<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'reviews';
    protected $primaryKey = 'review_id';
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'comment',
        'image_url',
        'created_at'
    ];

    // Quan hệ: 1 review thuộc về 1 user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ: 1 review thuộc về 1 product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
