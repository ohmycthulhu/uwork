<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Card\CreateCardFormRequest;
use App\Http\Requests\Card\UpdateCardFormRequest;
use App\Models\Payment\Card;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CardsController extends Controller
{
  /**
   * Card variable
   * @var Card
   */
  protected $card;

  /**
   * Creates instance os controller
   *
   * @param Card $card
   */
  public function __construct(Card $card)
  {
    $this->card = $card;
  }

  /**
   * Returns list of existing cards
   *
   * @return JsonResponse
   */
  public function get(): JsonResponse {
    $user = Auth::user();
    $cards = $user->cards()->get();

    return response()->json([
      'cards' => $cards,
    ]);
  }

  /**
   * Creates new card
   *
   * @param CreateCardFormRequest $request
   *
   * @return JsonResponse
   */
  public function create(CreateCardFormRequest $request): JsonResponse {
    $user = Auth::user();

    $card = $user->cards()->create($request->validated());

    return response()->json([
      'card' => $card,
      'status' => 'success',
    ]);
  }

  /**
   * Updates information about card
   *
   * @param UpdateCardFormRequest $request
   * @param int $cardId
   *
   * @return JsonResponse
   */
  public function update(UpdateCardFormRequest $request, int $cardId): JsonResponse {
    $user = Auth::user();
    $card = $user->cards()->find($cardId);

    if (!$card) {
      return response()->json(['error' => 'card does not not exists'], 403);
    }

    $card->updateInfo(
      $request->input('label'),
      $request->input('expiration_month'),
      $request->input('expiration_year'),
    );

    return response()->json([
      'card' => $card,
      'status' => 'success',
    ]);
  }

  /**
   * Deletes information about card
   *
   * @param int $cardId
   *
   * @return JsonResponse
   */
  public function delete(int $cardId): JsonResponse {
    $user = Auth::user();
    $card = $user->cards()->find($cardId);

    $card->delete();

    return response()->json([
      'status' => 'success',
      'card_found' => !!$card,
    ]);
  }
}
