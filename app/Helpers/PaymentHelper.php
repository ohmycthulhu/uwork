<?php


namespace App\Helpers;

use App\Models\Transactions\Transaction;
use App\Utils\CacheAccessor;
use Exception;
use Illuminate\Support\Str;

class PaymentHelper
{
  /**
   * Ongoing payment info
   * @var CacheAccessor
  */
  protected $storeActive;

  /**
   * Cancel keys
   * @var CacheAccessor
  */
  protected $storeCancel;

  /**
   * Transaction model
   * @var Transaction
  */
  protected $transaction;

  /**
   * Creates new instance
   *
   * @param Transaction $transaction
  */
  public function __construct(Transaction $transaction)
  {
    $this->storeActive = new CacheAccessor("payment-helper-active", null, 240);
    $this->storeCancel = new CacheAccessor("payment-helper-cancel", null, 240);
    $this->transaction = $transaction;
  }

  /**
   * Initializes new payment
   *
   * @param float $price
   *
   * @return ?array
  */
  public function initialize(float $price): ?array {
    // Generate uuid
    $uuid = Str::uuid();

    // Send request to bank
    $remoteData = $this->initializeRemote($price);
    if (!$remoteData) {
      return null;
    }

    // If successful, return array with needed information
    $data = [
      'payment_id' => $remoteData['payment_id'],
      'payment_url' => $remoteData['payment_url'],
      'price' => $price,
    ];

    $this->storeActive->set($uuid, $data);
    $this->storeCancel->set($remoteData['payment_id'], $uuid);

    return array_merge($data, ['uuid' => $uuid]);
  }

  /**
   * Cancels payment
   *
   * @param string $id
   *
   * @return bool
  */
  public function cancel(string $id): bool {
    // Check if payment with this id exists
    $uuid = $this->storeCancel->get($id);

    // If not, return false
    if (!$uuid) {
      return false;
    }

    $data = $this->storeActive->get($uuid);
    if (!$data) {
      return false;
    }

    // If yes, send cancel request
    if (!$this->cancelRemote($id)) {
      return false;
    }

    $this->storeCancel->remove($id);
    $this->storeActive->remove($uuid);

    // If succeeded, save transaction and return true
    $this->saveInformation($id, $data['price'], config('payment.status.canceled'));

    return true;
  }

  /**
   * Confirms payment
   *
   * @param string $uuid
   *
   * @return bool
  */
  public function confirm(string $uuid): bool {
    // Check if payment with this uuid exists
    $data = $this->storeActive->get($uuid);

    if (!$data) {
      return false;
    }

    // Check if we already have similar request
    $payment = $this->transaction::query()
      ->payment($data['payment_id'])
      ->first();
    if ($payment) {
      return false;
    }

    // Send request to bank to ensure the state
    if ($this->checkTransactionState($data['payment_id']) !== config('payment.status.undefined')) {
      return false;
    }

    // If succeeded, save information and return true
    $this->saveInformation($data['payment_id'], $data['price'], config('payment.status.confirmed'));

    $this->storeActive->remove($uuid);
    $this->storeCancel->remove($data['payment_id']);

    return true;
  }

  /**
   * Saves payment information
   *
   * @param string $id
   * @param float $price
   * @param string $status
   *
   * @return Transaction|bool
  */
  protected function saveInformation(string $id, float $price, string $status) {
    // Try to save information about transaction
    try {
      return Transaction::make($id, $price, $status);
    } catch (Exception $exception) {
      return false;
    }
  }

  /**
   * Initialize bank transaction
   *
   * @param float $price
   *
   * @return array
   * @noinspection PhpUnusedParameterInspection
   */
  protected function initializeRemote(float $price): array {
    // In future, send request to Tinkoff API

    // For now, return some information
    $id = Str::random(8);
    return [
      'payment_id' => $id,
      'payment_url' => "https://example.com/id=$id",
    ];
  }

  /**
   * Cancels bank transaction
   *
   * @param string $id
   *
   * @return bool
   * @noinspection PhpUnusedParameterInspection
   */
  protected function cancelRemote(string $id): bool {
    // In future, send request to Tinkoff API

    // For now, return true
    return true;
  }

  /**
   * Checks transaction state
   *
   * @param string $id
   *
   * @return string
   * @noinspection PhpUnusedParameterInspection
   */
  public function checkTransactionState(string $id): string {
    // In future, send request to Tinkoff API

    // For now, return undefined
    return config('payment.status.undefined');
  }
}