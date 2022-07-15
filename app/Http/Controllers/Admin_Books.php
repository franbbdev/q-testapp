<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;


class Admin_Books extends Controller
{
    /**
     * Show user profile or redirect to login
     *
     * @return string
     */
    public function delete_book() {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        $book_id = Arr::get($_POST, 'book_id');

        // No need to check, if vars did not exist, check_login would return login form.
        $access_token = Crypt::decryptString(session('access_token'));
        $api_url = config('services.q_sym_skeletal.api_url');


        $book_deleted = Http::withToken($access_token)->delete($api_url . 'books/' . $book_id);
        $book_deleted_status_code = $book_deleted->getStatusCode();
        if ($book_deleted_status_code == 204) {
            return json_encode(['success' => true, 'message' => 'Book deleted']);
        }

        return json_encode(['success' => false, 'message' => 'Book could not be deleted!']);

    }

    public function show_add_book_form() {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        $all_authors = Admin_Authors::get_all_authors();

        return view('books.books', ['all_authors' => $all_authors]);
    }

    public function add_book() {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        // No need to check, if vars did not exist, check_login would return login form.
        $access_token = Crypt::decryptString(session('access_token'));
        $api_url = config('services.q_sym_skeletal.api_url');
        $data = $_POST;
        $date_released = strtotime(Arr::get($data, 'book_release_date'));
        $dt = Carbon::parse($date_released);
        $new_book_data = [
            'author' => [
              'id' => (int)Arr::get($data, 'book_author', 0),
            ],
            'title' => Arr::get($data, 'book_title'),
            'release_date' => $dt->format('Y-m-d\TH:i:s.u\Z'),
            'description' => Arr::get($data, 'book_description'),
            'isbn' => Arr::get($data, 'book_isbn'),
            'format' => Arr::get($data, 'book_format'),
            'number_of_pages' => (int)Arr::get($data, 'no_of_pages'),
        ];

        $book_created = Http::withToken($access_token)->post($api_url . 'books', $new_book_data);
        $book_created_status_code = $book_created->getStatusCode();
        if ($book_created_status_code == 200) {
            return json_encode(['success' => true, 'message' => 'Book created']);
        } else {
            return json_encode(['success' => false, 'message' => 'Book could not be created, please check input fields and try again!']);
        }

    }
}
