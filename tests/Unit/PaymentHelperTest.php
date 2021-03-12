<?php

namespace Tests\Unit;

use App\Facades\PaymentFacade;
use App\Models\Transactions\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentHelperTest extends \Tests\TestCase
{
  use RefreshDatabase;

  /**
   * A basic unit test example.
   *
   * @return void
   */
  public function testCreation()
  {
    $price = rand(10, 100);

    // Create payment
    $paymentUuid = $this->createPayment($price)['uuid'];

    // Confirm payment
    $this->assertTrue($this->confirmPayment($paymentUuid));

    // Try to confirm payment
    $this->assertFalse($this->confirmPayment($paymentUuid));

    // Check database
    $this->checkTransactionsCount(1);
  }

  /**
   * Test for checking canceling
   *
   * @return void
   */
  public function testCancel()
  {
    $price = rand(10, 100);

    // Create payment
    $paymentInfo = $this->createPayment($price);
    $paymentId = $paymentInfo['payment_id'];
    $paymentUuid = $paymentInfo['uuid'];

    // Cancel payment
    $this->assertTrue(
      $this->cancelPayment($paymentId)
    );

    // Try to confirm payment
    $this->assertFalse(
      $this->cancelPayment($paymentId)
    );

    // Check database
    $this->checkTransactionsCount(1);
  }

  /**
   * Test for checking canceling of confirmed transactions
   *
   * @return void
   */
  public function testConfirmation()
  {
    $price = rand(10, 100);

    // Create payment
    $paymentInfo = $this->createPayment($price);
    $paymentId = $paymentInfo['payment_id'];
    $paymentUuid = $paymentInfo['uuid'];

    // Confirm payment
    $this->assertTrue(
      $this->confirmPayment($paymentUuid)
    );

    // Try to cancel payment
    $this->assertFalse(
      $this->cancelPayment($paymentId)
    );

    // Check database
    $this->checkTransactionsCount(1);
  }

  /**
   * Creates payment and checks if data is correct
   *
   * @param float $price
   *
   * @return array
  */
  protected function createPayment(float $price): array {
    $data = PaymentFacade::initialize($price);

    $this->assertArrayHasKey('payment_id', $data);
    $this->assertArrayHasKey('payment_url', $data);
    $this->assertArrayHasKey('uuid', $data);

    return $data;
  }

  /**
   * Cancels payment
   *
   * @param string $id
   *
   * @return bool
  */
  protected function cancelPayment(string $id): bool {
    return PaymentFacade::cancel($id);
  }

  /**
   * Confirms payment
   *
   * @param string $uuid
   *
   * @return bool
  */
  protected function confirmPayment(string $uuid): bool {
    return PaymentFacade::confirm($uuid);
  }

  /**
   * Ensure transactions count
   *
   * @param int $number
   *
   * @return void
  */
  protected function checkTransactionsCount(int $number) {
    $this->assertDatabaseCount((new Transaction)->getTable(), $number);
  }
}
