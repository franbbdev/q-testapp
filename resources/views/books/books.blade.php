@include('shared.head')
<h1>
    Add book
</h1>

@if(!empty($all_authors))
    <form class="add_book" action="/add-book-ajax">
        <div id="success"
             style="background: green; color: white; display: block; margin: auto; padding: 10px 0; display: none;"></div>
        <div id="error"
             style="background: red; color: white; display: block; margin: auto; padding: 10px 0; display: none;"></div>
        @csrf
        <div class="field_group">
            <label for="book_title">Book title</label><br>
            <input type="text" name="book_title">
        </div>
        <div class="field_group">
            <label for="book_release_date">Release date</label><br>
            <input type="date" name="book_release_date">
        </div>
        <div class="field_group">
            <label for="book_description">Book description</label><br>
            <textarea rows="8" name="book_description"></textarea>
        </div>
        <div class="field_group">
            <label for="book_isbn">ISBN</label><br>
            <input type="text" name="book_isbn">
        </div>
        <div class="field_group">
            <label for="book_format">Book format</label><br>
            <input type="text" name="book_format">
        </div>
        <div class="field_group">
            <label for="no_of_pages">Number of pages</label><br>
            <input type="number" name="no_of_pages" id="authors_limit_results">
        </div>
        <div class="field_group">
            <label for="book_author">Choose author</label><br>
            <select name="book_author" id="authors_sort">
                @foreach($all_authors AS  $author_id => $author_name)
                    <option value="{{$author_id}}">{{$author_name}}</option>
                @endforeach
            </select>
        </div>
        <input type="submit" value="Add book">
    </form>
@else
    No authors found!
@endif


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script type="text/javascript">

    $('form.add_book').on('submit', function (e) {
        e.preventDefault();
        $('#success, #error').hide();
        var $theForm = $(this);
        let book_title = $('input[name="book_title"]').val();
        let book_release_date = $('input[name="book_release_date"]').val();
        let book_description = $('textarea[name="book_description"]').val();
        let book_isbn = $('input[name="book_isbn"]').val();
        let book_format = $('input[name="book_format"]').val();
        let no_of_pages = $('input[name="no_of_pages"]').val();
        let book_author = $('select[name="book_author"]').val();

        $.ajax({
            url: "/add-book",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                book_title: book_title,
                book_release_date: book_release_date,
                book_description: book_description,
                book_isbn: book_isbn,
                book_format: book_format,
                no_of_pages: no_of_pages,
                book_author: book_author,
            },
            success: function (response) {
                if (response) {
                    var response_data = $.parseJSON(response);
                    if (response_data.success == true) {
                        $('#success').text(response_data.message).fadeIn(300).delay(1000);
                        window.location.href = "/author/" + book_author + "#" + book_isbn;
                    } else {
                        $('#error').text(response_data.message).fadeIn(300);
                    }
                }
            },
            error: function (response) {
                $('#login-error').text('Book could not be created, please check form data!');
            }
        });
    });
</script>
@include('shared.footer')
