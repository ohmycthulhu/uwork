<?php

namespace App\Models\Transactions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'payment_id', 'price', 'status',
    ];

    /**
     * Function to create transaction
     *
     * @param string $id
     * @param float $price
     * @param ?string $status
     *
     * @return Transaction
    */
    public static function make(string $id, float $price, ?string $status): Transaction {
      return Transaction::create([
        'payment_id' => $id,
        'price' => $price,
        'status' => $status,
      ]);
    }

    /**
     * Scope to filter via payment id
     *
     * @param Builder $query
     * @param string $paymentId
     *
     * @return Builder
    */
    public function scopePayment(Builder $query, string $paymentId): Builder {
      return $query->where('payment_id', $paymentId);
    }
}
