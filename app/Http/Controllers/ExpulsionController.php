<?php

namespace App\Http\Controllers;

use App\Models\Expulsion;
use Illuminate\Http\Request;

class ExpulsionController extends Controller {

  public function getExpulsionesByUserId(Request $request) {
    return Expulsion::where('user_id',$request->user_id)->get();
  }

}