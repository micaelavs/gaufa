<?php

namespace App\Http\Helper;

use Illuminate\Support\Facades\Http;

class LoginApi
{
  public $accessToken;

  public function __construct()
  {
    $response = Http::post(config('api.config.endPointAPIPanel') . '/loginApi', ['username' => config('api.config.username_api'), 'password' => config('api.config.password_api')]);
    if (!$response->failed()) {
      $this->accessToken = !is_null($token = $response->json('token')) ? $response->json('token') : null;
    }
  }

  public function getToken()
  {
    return $this->accessToken;
  }
}
