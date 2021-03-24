<?php


namespace App\Helpers;

use App\Models\User;
use App\Models\User\Notification;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

/**
 * Class that encapsulates interaction with notifications
 */
class NotificationHelper
{
  /**
   * Notification model
   * @var Notification $notification
   */
  protected $notification;


  /**
   * Creates instance of helper
   *
   * @param Notification $notification
   *
  */
  public function __construct(Notification $notification)
  {
    $this->notification = $notification;
  }

  /**
   * Gets all notifications associated with user
   *
   * @param User $user
   * @param bool $unreadOnly
   * @param int $paginationSize
   *
   * @return Paginator
  */
  public function getByUser(User $user, bool $unreadOnly = false, int $paginationSize = 15): Paginator {
    $query = $user->notifications();

    if ($unreadOnly) {
      $query->unread();
    }

    return $query->paginate($paginationSize);
  }

  /**
   * Creates new notification
   *
   * @param User $user
   * @param string $class
   * @param int $id
   * @param string|array $title
   * @param string|array|null $description
   *
   * @return Notification
  */
  public function create(
    User $user,
    string $class,
    int $id,
    $title,
    $description
  ): Model {
    return $user->notifications()
      ->create([
        'notifiable_type' => $class,
        'notifiable_id' => $id,
        'title' => $title,
        'description' => $description
      ]);
  }

  /**
   * Marks notifications as read
   *
   * @param User $user
   * @param ?array $ids
   *
   * @return int
  */
  public function markRead(User $user, ?array $ids = null): int {
    $query = $user->notifications()->unread();

    if ($ids !== null) {
      $query->ids($ids);
    }

    return $query->update(['read_at' => date('Y-m-d H:i:s')]);
  }
}