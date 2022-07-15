@include('shared.head')
<h1>Authors</h1>

@if(session()->has('authors_message'))
    <div id="success" style="background: green; color: white; display: block; margin: auto; float: left; clear: both; margin-bottom: 10px; padding: 5px 10px;">
        {{session()->pull('authors_message')}}
    </div>
@endif

<div id="error"
     style="background: red; color: white; display: block; margin: auto; display: none; float: left; clear: both; margin-bottom: 10px; padding: 5px 10px;"></div>

<form class="authors_sort" method="GET" action="/authors/{{$authors_data['current_page']}}" style="clear: both;">
    @csrf
    <label for="sort">Sort results</label>
    <select name="sort" id="authors_sort">
        <option @if ($authors_data['direction'] == 'ASC') selected @endif value="ASC">ASC</option>
        <option @if ($authors_data['direction'] == 'DESC') selected @endif value="DESC">DESC</option>
    </select>

    <label for="sort_by">Sort by</label>
    <select name="sort_by" id="authors_sort_by">
        <option @if ($authors_data['order_by'] == 'id') selected @endif value="id">ID</option>
        <option @if ($authors_data['order_by'] == 'birthday') selected @endif value="birthday">Date of birth</option>
        <option @if ($authors_data['order_by'] == 'gender') selected @endif value="gender">Gender</option>
    </select>

    <label for="limit_results">Limit results</label>
        <input type="number" name="limit_results" id="authors_limit_results" value="{{$authors_data['limit']}}">

    <input type="submit" value="Sort results">
</form>

<table>
    <tr>
        <td>Number</td>
        <td>ID</td>
        <td>First Name</td>
        <td>Last Name</td>
        <td>Born</td>
        <td>Gender</td>
        <td>Place of birth</td>
        <td>View</td>
        <td>Delete</td>
    </tr>

    @if(empty($authors_data['items']))
        <tr><td>No Authors found!</td></tr>
    @else
        @php
            $authors = $authors_data['items'];
        @endphp
    @endif

    @if(!empty($authors))
        @php
        if (Arr::get($authors_data, 'current_page') > 1) {
            $i = (($authors_data['current_page'] - 1) * Arr::get($authors_data, 'limit')) + 1;
        } else {
            $i = 1;
        }
            $z = 0;
        @endphp
        @foreach($authors AS $author)
            <tr>
                <td>{{$i}}</td>
                <td>{{Arr::get($author, 'id')}}</td>
                <td>{{Arr::get($author, 'first_name')}}</td>
                <td>{{Arr::get($author, 'last_name')}}</td>
                <td>{{Carbon\Carbon::parse(Arr::get($author, 'birthday'))->format('d.m.Y.')}}</td>
                <td>{{Arr::get($author, 'gender')}}</td>
                <td>{{Arr::get($author, 'place_of_birth')}}</td>
                <td><a class="view_item" href="/author/{{Arr::get($author, 'id')}}">View</a></td>
                <td><a href="#" class="delete_inline" data-delete_author="{{Arr::get($author, 'id')}}">Delete</a></td>
            </tr>
            @php
               $i++
            @endphp
        @endforeach
    @endif
</table>
@if(Arr::get($authors_data, 'total_pages', 1) > 1)
    @if($authors_data['current_page'] > 1)
        <a href="/authors/{{$authors_data['current_page'] - 1}}?sort={{app('request')->input('sort')}}&sort_by={{app('request')->input('sort_by')}}&limit_results={{app('request')->input('limit_results')}}" class="pagination_prev">Previous</a>
    @endif
    <ul class="pagination">
        @while ($z < Arr::get($authors_data, 'total_pages'))
            @if(($z + 1) == $authors_data['current_page'])
                @php
                    $pagination_item_class = 'active';
                @endphp
            @else
                @php
                    $pagination_item_class = 'inactive';
                @endphp
            @endif

            <li><a class="{{$pagination_item_class}}" href="/authors/{{$z+1}}?sort={{app('request')->input('sort')}}&sort_by={{app('request')->input('sort_by')}}&limit_results={{app('request')->input('limit_results')}}">{{$z+1}}</a></li>

            @php
                $z++
            @endphp
        @endwhile
    </ul>
    @if($authors_data['current_page'] < $authors_data['total_pages'])
        <a href="/authors/{{$authors_data['current_page'] + 1}}?sort={{app('request')->input('sort')}}&sort_by={{app('request')->input('sort_by')}}&limit_results={{app('request')->input('limit_results')}}" class="pagination_next">Next</a>
    @endif
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script type="text/javascript">

    $('a[data-delete_author]').on('click', function (e) {
        e.preventDefault();
        $('#success, #error').hide();
        var author_id = $(this).attr('data-delete_author');

        $.ajax({
            url: "/delete-author",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                author_id: author_id,
            },
            success: function (response) {
                console.log(response);
                if (response) {
                    try {
                        response_data = JSON.parse(response);
                    } catch (e) {
                        response_data = response;
                    }
                    console.log('Success:' + response_data.success);
                    console.log('Message:' + response_data.message);
                    if (response_data.success == true) {
                        location.reload();
                    } else {
                        $('#error').text(response_data.message).fadeIn(300);
                    }
                }
            },
            error: function (response) {
                $('#login-error').text('Could not login, please check username and password and try again!');
            }
        });
    });
</script>
@include('shared.footer')
