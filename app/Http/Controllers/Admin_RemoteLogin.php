<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;


class Admin_RemoteLogin extends Controller
{
    /**
     * Check if user logged in
     *
     * @return array
     */
    public static function check_login()
    {
        $current_user_details = (new self())->get_me();

        if (empty($current_user_details) OR empty($current_user_details[0]) OR empty($current_user_details[1])) {
            return (new self())->show_login_form();
        }

        return [true, $current_user_details[1]];
    }

    public static function refresh_access_token() {
        $api_url = config('services.q_sym_skeletal.api_url');
        if (!empty(session('refresh_token'))) {
            $refresh_token = Crypt::decryptString(session('refresh_token'));
        }

        if (empty($refresh_token)) {
            return ['redirect_to_login', 'Redirect to login'];
        }

        if (empty($api_url)) {
            return [false, 'API URL missing!'];
        }

        $ref_token_response = Http::acceptJson()->get($api_url . 'token/refresh/' . $refresh_token);
        $ref_token_response_arr = json_decode($ref_token_response, true);
        $new_access_token = Arr::get($ref_token_response_arr, 'token_key');

       if (empty($new_access_token)) {
            return [false, Arr::get($ref_token_response_arr, 'detail', 'Access token could not be refreshed!')];
        }

        $new_access_token = Crypt::encryptString($new_access_token);
        $api_token_expire = config('services.q_sym_skeletal.api_token_expire');

        session(['access_token' => $new_access_token, 'access_token_expire' => strtotime($api_token_expire)]);

        return [true, $new_access_token];
    }

    public static function get_access_token($username, $password) {
        $api_config = config('services.q_sym_skeletal');
        $post_fields = [
            'email' => $username,
            'password' => $password,
        ];

        if (empty($api_config['api_url'])) {
            return [false, 'API URL missing!'];
        }

        $access_token_response = Http::acceptJson()->post($api_config['api_url'] . 'token', $post_fields);
        $access_token_response_arr = json_decode($access_token_response, true);
        $new_access_token = (!empty(Arr::get($access_token_response_arr, 'token_key'))) ? $access_token_response_arr['token_key'] : false;

        if (empty($new_access_token)) {
            return [false, Arr::get($access_token_response_arr, 'detail', 'Unexpected error occured!')];
        }

        $new_refresh_token = (!empty(Arr::get($access_token_response_arr, 'refresh_token_key'))) ? $access_token_response_arr['refresh_token_key'] : false;
        $new_access_token = Crypt::encryptString($new_access_token);
        $new_refresh_token = Crypt::encryptString($new_refresh_token);
        $api_token_expire = config('services.q_sym_skeletal.api_token_expire');
        $expire_timestamp = strtotime('now +' . $api_token_expire);
        session(['access_token' => $new_access_token, 'refresh_token' => $new_refresh_token, 'access_token_expire' => $expire_timestamp]);

        return  [true, $new_access_token];

    }

    public function get_me() {
        if (!empty(session('access_token'))) {
            $access_token = Crypt::decryptString(session('access_token'));
        }
        if (!empty(session('access_token_expire'))) {
            $access_token_expire = session('access_token_expire');
        }

        if (empty($access_token) OR empty($access_token_expire) OR $access_token_expire > time()) {
            $access_token = self::refresh_access_token();
            if (empty($access_token)) {
                return [false, 'Access token invalid and could not be refrefreshed'];
            }
        }

        if (!empty($access_token[0]) AND $access_token[0] == 'redirect_to_login') {
            return [false, 'Redirect to login!'];
        }

        if (is_array($access_token) AND empty($access_token[0])) {
            return [false, $access_token[1]];
        }

        if (empty(config('services.q_sym_skeletal.api_url'))) {
            return [false, 'API URL missing!'];
        }

        $api_url = config('services.q_sym_skeletal.api_url');

        $current_user_data = Http::withToken($access_token)->get($api_url . 'me');

        return [true, json_decode($current_user_data, true)];
    }

    public function show_login_form() {
        return view('auth/login');
    }

    public function remote_log_in() {
        $username = Arr::get($_POST, 'email');
        $password = Arr::get($_POST, 'password');

        if (empty($username) OR empty($password)) {
            return json_encode(['success' => false, 'message' => 'Both fields are mandatory, please check input and try again!']);
        }

        $login = self::get_access_token($username, $password);

        if (empty($login[0])) {
            return json_encode(['success' => false, 'message' => $login[1]]);
        }

        return json_encode(['success' => true, 'message' => 'Login successful!']);
    }

    public function log_out(Request $request) {
        $request->session()->flush();
        return $this->show_login_form();
    }
}
