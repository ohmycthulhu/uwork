<?php

namespace App\Http\Controllers\API\Info;

use App\Http\Controllers\Controller;
use App\Models\Complaints\ComplaintType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintTypesController extends Controller
{
    /* @var ComplaintType $complaintType */
  protected $complaintType;

  /**
   * Creates an instance
   *
   * @param ComplaintType $complaintType
  */
  public function __construct(ComplaintType $complaintType)
  {
    $this->complaintType = $complaintType;
  }

  /**
   * Method to get all types
   *
   * @return JsonResponse
  */
  public function index(): JsonResponse {
    $types = $this->complaintType::all();
    return $this->returnSuccess(compact('types'));
  }
}
