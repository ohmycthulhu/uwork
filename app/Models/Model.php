<?php


namespace App\Models;

use App\Models\Traits\HasBatchExecute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BasicModel;

class Model extends BasicModel
{
  use HasBatchExecute;

  protected function asJson($value)
  {
    return json_encode($value, JSON_UNESCAPED_UNICODE);
  }
}