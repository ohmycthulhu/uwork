<?php

namespace App\Models\Payment;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes;

    protected $fillable = [
      'number', 'expiration_month', 'expiration_year', 'cvv', 'name', 'label'
    ];

    protected $hidden = [
      'number', 'cvv', 'name'
    ];

    protected $appends = [
      'number_obfuscated'
    ];

    /**
     * Method to update card information
     *
     * @param ?string $label
     * @param ?int $expirationMonth
     * @param ?int $expirationYear
     *
     * @return $this
    */
    public function updateInfo(?string $label = null, ?int $expirationMonth = null, ?int $expirationYear = null): Card {
      if ($label) {
        $this->label = $label;
      }
      if ($expirationMonth) {
        $this->expiration_month = $expirationMonth;
      }
      if ($expirationYear) {
        $this->expiration_year = $expirationYear;
      }

      if ($this->isDirty()) {
        $this->save();
      }

      return $this;
    }


    /**
     * Relation to user
     *
     * @return BelongsTo
    */
    public function user(): BelongsTo {
      return $this->belongsTo(User::class);
    }

    /**
     * Attribute to obfuscate number
     *
     * @return string
    */
    public function getNumberObfuscatedAttribute(): string {
      $number = $this->number ?? '';
      return substr($number, 0, 4).str_repeat('*', 8).substr($number, 12);
    }
}
