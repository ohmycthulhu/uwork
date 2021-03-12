<?php

namespace Tests\Unit;

use App\Utils\CacheAccessor;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class CacheAccessorTest extends \Tests\TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testBasic()
    {
      // Generate some value
      $value = Str::random();
      $defaultValue = Str::random();
      $prefix = "test";
      $key = Str::uuid();

      // Create accessor
      $access = new CacheAccessor($prefix, $defaultValue);

      // Check default values
      $this->assertEquals(
        $defaultValue,
        $access->get(Str::uuid())
      );

      $this->assertEquals(
        $value,
        $access->get(Str::uuid(), $value)
      );

      // Set value
      $access->set($key, $value);

      // Get value
      // Check if value is equivalent
      $this->assertEquals(
        $value,
        $access->get($key)
      );

      // Remove value
      $access->remove($key);

      // Check if value is stored
      $this->assertEquals(
        $defaultValue,
        $access->get($key)
      );
    }
}
