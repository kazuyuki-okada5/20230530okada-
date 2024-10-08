<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    // 大量代入の際に保護される属性リストを指定する。
    protected $guarded = ['id'];
    // バリデーションルールを適用。
    public static $rules = [
        'user_id' => 'required',
        'condition_id' => 'required',
        'name' => 'required|max:50',
        'price' => 'required|integer',
        'comment' => 'required',
        'image_url' => 'required|max:255',
        'brand' => 'nullable|max:50',
        'sold_user_id' => 'nullable',
    ];

    // Itemsテーブルのuser_idカラムを使用しUsersテーブルのレコードを参照。
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Itemsテーブルのcondition_idカラムを使用しConditionsテーブルのレコードを参照。
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }

    // itemsテーブルのsold_user_idカラムを使用して、users`テーブルのレコードを参照。
    public function soldUser()
    {
        return $this->belongsTo(User::class, 'sold_user_id');
    }

    // Likesテーブルにidカラムを取得させItemsテーブルのレコードを参照
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // likesテーブルを通じて、ユーザーとの多対多のリレーションを定義。
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    // Commentsテーブルにidカラムを取得させItemsテーブルのレコードを参照。
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Category_Itemsテーブルにidカラムを取得させItemsテーブルのレコードを参照。
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items', 'item_id', 'category_id');
    }

    // shipping_addressesテーブルのレコードを参照。
    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class);
    }

    // paymentsテーブルのレコードを参照。
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
