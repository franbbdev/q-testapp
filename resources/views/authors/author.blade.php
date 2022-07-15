@include('shared.head')
<h1>
    {{Arr::get($author_data, 'first_name')}} {{Arr::get($author_data, 'last_name')}}
</h1>
<p>Born {{Carbon\Carbon::parse(Arr::get($author_data, 'birthday'))->format('d.m.Y.')}} in {{Arr::get($author_data, 'place_of_birth')}}</p>

@if(!empty($author_data['books']))
    @php
        $books = $author_data['books'];
    @endphp
    <h2>Books({{count($books)}}):</h2>
    @foreach($books AS $book)
        <article id="{{Arr::get($book, 'isbn')}}" data-book_id={{Arr::get($book, 'id')}} style="border: 1px solid black; padding: 30px; margin-bottom: 30px;">
            <h1>{{Arr::get($book, 'title')}}</h1>
            <ul>
                <li>Released: {{Carbon\Carbon::parse(Arr::get($book, 'release_date'))->format('d.m.Y.')}}</li>
                <li>ISBN: {{Arr::get($book, 'isbn')}}</li>
                <li>Format: {{Arr::get($book, 'format')}}</li>
                <li>Number of pages: {{Arr::get($book, 'number_of_pages')}}</li>
            </ul>
            <p>{{Arr::get($book, 'description')}}</p>
            <button class="delete_book" data-delete_book="{{Arr::get($book, 'id')}}">Delete book</button>
        </article>
    @endforeach
@else
    This author has no books and can be deleted!
@endif
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script type="text/javascript">

    $('button[data-delete_book]').on('click', function (e) {
        e.preventDefault();
        $('#success, #error').hide();
        var book_id = $(this).attr('data-delete_book');

        $.ajax({
            url: "/delete-book",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                book_id: book_id,
            },
            success: function (response) {
                console.log(response);
                if (response) {
                    var response_data = $.parseJSON(response);
                    console.log('Success:' + response_data.success);
                    console.log('Message:' + response_data.message);
                    if (response_data.success == true) {
                        $('article[data-book_id="' + book_id + '"]').fadeOut(300).delay(100).remove();
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
