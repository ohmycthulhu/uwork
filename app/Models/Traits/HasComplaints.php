<?php


namespace App\Models\Traits;


use App\Models\Complaints\Complaint;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasComplaints
{

  /**
   * Relation to complaints
   *
   * @return MorphMany
   */
  public function complaints(): MorphMany {
    return $this->morphMany(Complaint::class, 'complaintable');
  }

  /**
   * Creates new complaint
   *
   * @param ?User $user
   * @param ?string $ip
   * @param ?int $complaintTypeId
   * @param ?string $complaintReason
   * @param string $text
   *
   * @return ?Complaint
   */
  public function createComplaint(
    ?User $user,
    ?string $ip,
    ?int $complaintTypeId,
    ?string $complaintReason,
    string $text
  ): ?Model {
    // Check if similar exists
    // If yes, return null
    if ($this->complaints()->similar($user, $ip)->count() > 0) {
      return null;
    }

    // If not, create new and return
    return $this->complaints()->create([
      'user_id' => $user ? $user->id : null,
      'ip_addr' => $ip,
      'type_id' => $complaintTypeId,
      'reason_other' => $complaintReason,
      'text' => $text,
    ]);
  }
}