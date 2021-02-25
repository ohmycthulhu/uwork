<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model as BasicModel;

class Model extends BasicModel
{
  protected function asJson($value)
  {
    return json_encode($value, JSON_UNESCAPED_UNICODE);
  }
}