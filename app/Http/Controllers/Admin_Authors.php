<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;


class Admin_Authors extends Controller
{
    /**
     * Show user profile or redirect to login
     *
     * @return \Illuminate\View\View
     */
    public function list_authors($page_no = 1)
    {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        // No need to check, if vars did not exist, check_login would return login form.
        $access_token = Crypt::decryptString(session('access_token'));
        $api_url = config('services.q_sym_skeletal.api_url');
        $params = [
            'orderBy' => (!empty(Arr::get($_GET, 'sort_by'))) ? $_GET['sort_by'] : 'id',
            'direction' => (!empty(Arr::get($_GET, 'sort'))) ? $_GET['sort'] : 'ASC',
            'limit' => (!empty(Arr::get($_GET, 'limit_results'))) ? $_GET['limit_results'] : 10,
            'page' => (!empty($page_no)) ? (int)$page_no : 1,
        ];

        $authors = Http::withToken($access_token)->get($api_url . 'authors', $params);

        $authors_data = json_decode($authors, true);

        return view('authors.authors', ['authors_data' => $authors_data]);

    }

    public function single_author($author_id, $return_view = true)
    {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        // No need to check, if vars did not exist, check_login would return login form.
        $access_token = Crypt::decryptString(session('access_token'));
        $api_url = config('services.q_sym_skeletal.api_url');


        $author = Http::withToken($access_token)->get($api_url . 'authors/' . $author_id);

        $author_data = json_decode($author, true);

        if (!empty($return_view)) {
            return view('authors.author', ['author_data' => $author_data]);
        } else {
            return $author_data;
        }

    }

    public static function get_all_authors() {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        // No need to check, if vars did not exist, check_login would return login form.
        $access_token = Crypt::decryptString(session('access_token'));
        $api_url = config('services.q_sym_skeletal.api_url');
        $all_authors = [];
        $get_page = 1;
        $params = [
            'orderBy' => 'id',
            'direction' => 'ASC',
            'limit' => 10,
            'page' => $get_page,
        ];

        $authors = Http::withToken($access_token)->get($api_url . 'authors', $params);
        $authors_data = json_decode($authors, true);

        $get_page++;

        if (!empty($authors_data['items'])) {
            foreach ($authors_data['items'] AS $author_data) {
                $all_authors[$author_data['id']] = $author_data['first_name'] . ' ' . $author_data['last_name'];
            }
        }

        if ($authors_data['current_page'] < $authors_data['total_pages']) {
            while ($get_page <= $authors_data['total_pages']) {
                $params = [
                    'orderBy' => 'id',
                    'direction' => 'ASC',
                    'limit' => 10,
                    'page' => $get_page,
                ];

                $authors = Http::withToken($access_token)->get($api_url . 'authors', $params);
                $authors_data = json_decode($authors, true);

                $get_page++;

                if (!empty($authors_data['items'])) {
                    foreach ($authors_data['items'] AS $author_data) {
                        $all_authors[$author_data['id']] = $author_data['first_name'] . ' ' . $author_data['last_name'];
                    }
                }
            }
        }

        return $all_authors;
    }

    public function delete_author() {
        $user_logged_in = Admin_RemoteLogin::check_login();

        if (!is_array($user_logged_in)) {
            return $user_logged_in;
        }

        $author_id = Arr::get($_POST, 'author_id');

        $author_data = $this->single_author($author_id, false);

        if (!empty($author_data['books']))  {
            return ['success' => false, 'message' => 'Could not delete author. Delete his/her books first!'];
        }

        // No need to check, if vars did not exist, check_login would return login form.
        $access_token = Crypt::decryptString(session('access_token'));
        $api_url = config('services.q_sym_skeletal.api_url');


        $author_deleted = Http::withToken($access_token)->delete($api_url . 'authors/' . $author_id);
        $author_deleted_status_code = $author_deleted->getStatusCode();
        if ($author_deleted_status_code == 204) {
            session(['authors_message' => 'Author deleted!']);
            return json_encode(['success' => true, 'message' => 'Author deleted']);
        }

        return json_encode(['success' => false, 'message' => 'Author could not be deleted!']);

    }
}
