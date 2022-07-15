<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;


class Admin_RemoteUser extends Controller
{
    /**
     * Show user profile or redirect to login
     *
     * @return \Illuminate\View\View
     */
    public function show_user()
    {
        $is_logged_in = Admin_RemoteLogin::check_login();
        if (is_array($is_logged_in) AND !empty($is_logged_in[0]) AND !empty($is_logged_in[1])) {
            return view('users/user_profile', ['user_data' => $is_logged_in[1]]);
        } else {
            return $is_logged_in;
        }


    }


}
