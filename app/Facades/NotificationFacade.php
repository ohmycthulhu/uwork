<?php


namespace App\Facades;


use App\Models\User;
use App\Models\User\Notification;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for interacting with notifications
 *
 * @method static Paginator getByUser(User $user, bool $unreadOnly, int $paginationSize)
 * @method static Notification create(User $user, string $class, int $id, $title, ?string $description)
 * @method static int markRead(User $user, ?array $ids)
 * */
class NotificationFacade extends Facade
{
  /**
   * Facade key
  */
  protected static function getFacadeAccessor(): string
  {
    return 'notifications-helper';
  }
}