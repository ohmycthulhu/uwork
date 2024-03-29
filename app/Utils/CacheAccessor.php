<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;

class CacheAccessor
{
  /**
   * Cache key prefix
   * @var string
  */
  protected $prefix;

  /**
   * Global default
   * @var mixed
  */
  protected $default;

  /**
   * Expiration in minutes
   * @var int
  */
  protected $duration;

  /**
   * Creates new instance
   *
   * @param string $prefix
   * @param mixed $default
   * @param int   $duration
  */
  public function __construct(string $prefix, $default = null, int $duration = 60)
  {
    $this->prefix = $prefix;
    $this->default = $default;
    $this->duration = $duration;
  }

  /**
   * Gets value from cache
   *
   * @param string $key
   * @param mixed $default
   * @param bool $setOnFail
   *
   * @return mixed
  */
  public function get(string $key, $default = null, bool $setOnFail = false) {
    $value = Cache::get($this->generateKeyName($key));
    if ($value) {
      return $value;
    }
    $result = is_callable($default) ? call_user_func($default) : $default ?? $this->default;
    if ($setOnFail) {
      $this->set($key, $result);
    }
    return $result;
  }

  /**
   * Sets value in cache
   *
   * @param string $key
   * @param mixed $value
   *
   * @return bool
  */
  public function set(string $key, $value): bool {
    return Cache::put(
      $this->generateKeyName($key),
      $value,
      now()->addMinutes($this->duration)
    );
  }

  /**
   * Removes key from cache
   *
   * @param string $key
   *
   * @return bool
  */
  public function remove(string $key): bool {
    return Cache::forget($this->generateKeyName($key));
  }

  /**
   * Generates key name
   *
   * @param string $key
   *
   * @return string
  */
  protected function generateKeyName(string $key): string {
    return "{$this->prefix}-$key";
  }
}