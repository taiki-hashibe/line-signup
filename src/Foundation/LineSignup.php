<?php

namespace LowB\LineSignup\Foundation;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait LineSignUp
{
    public $lineSignupConfigKeys = [
        'channel_id' => 'line-signup.line_login.channel_id',
        'callback_url' => 'line-signup.line_login.callback_url',
        'client_secret' => 'line-signup.line_login.channel_secret'
    ];
    public string $lineSignupModelClass = '';
    public string $lineSignupGuard = 'web';
    public string $lineIdColumnName = 'line_id';
    public string $lineSignupRedirectRouteName = '';

    public function lineLogin(): RedirectResponse
    {
        $state = Str::random(32);

        $uri = "https://access.line.me/oauth2/v2.1/authorize?";
        $response_type = "response_type=code";
        $client_id = "&client_id=" . config($this->lineSignupConfigKeys['channel_id']);
        $redirect_uri = "&redirect_uri=" . config($this->lineSignupConfigKeys['callback_url']);
        $state_uri = "&state=" . $state;
        $scope = "&scope=openid%20profile";
        $prompt = "&prompt=consent";
        $nonce_uri = "&nonce=";

        $uri = $uri . $response_type . $client_id . $redirect_uri . $state_uri . $scope . $prompt . $nonce_uri;

        return redirect($uri);
    }

    public function getAccessToken($req): mixed
    {
        $headers = ['Content-Type: application/x-www-form-urlencoded'];
        $post_data = array(
            'grant_type'    => 'authorization_code',
            'code'          => $req['code'],
            'redirect_uri'  => config($this->lineSignupConfigKeys['callback_url']),
            'client_id'     =>  config($this->lineSignupConfigKeys['channel_id']),
            'client_secret' => config($this->lineSignupConfigKeys['client_secret']),
        );
        $url = 'https://api.line.me/oauth2/v2.1/token';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post_data));

        $res = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($res);
        dump($json);
        $accessToken = $json->access_token;

        return $accessToken;
    }

    public function getProfile($at): mixed
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $at));
        curl_setopt($curl, CURLOPT_URL, 'https://api.line.me/v2/profile');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $res = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($res);

        return $json;
    }

    public function callback(Request $request): RedirectResponse
    {
        $accessToken = $this->getAccessToken($request);
        $profile = $this->getProfile($accessToken);

        $model = app()->make($this->lineSignupModelClass);
        $user = $model->where($this->lineIdColumnName, $profile->userId)->first();

        if ($user) {
            Auth::guard($this->guard)->login($user);
        } else {
        }

        return redirect()->route($this->lineSignupRedirectRouteName);
    }

    public function accountExist(Authenticatable $user)
    {
        Auth::guard($this->guard)->login($user);
    }

    public function accountDoesNotExist(mixed $profile)
    {
        $model = app()->make($this->lineSignupModelClass);
        $model->provider = 'line';
        $model->line_id = $profile->userId;
        $model->name = $profile->displayName;
        $model->save();
        Auth::guard($this->guard)->login($model);
    }
}
