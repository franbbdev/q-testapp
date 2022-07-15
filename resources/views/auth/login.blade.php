@include('shared.head')
<div
    class="relative flex items-top justify-center min-h-screen sm:items-center py-4 sm:pt-0">
    <form action="/user" class="remote_login_form" id="remote_login_form" name="remote_login_form">
        <div id="success"
             style="background: green; color: white; display: block; margin: auto; padding: 10px 0; display: none;"></div>
        <div id="error"
             style="background: red; color: white; display: block; margin: auto; padding: 10px 0; display: none;"></div>
        <div class="form_group form_group-email">
            <label for="email">Email:</label>
            <input type="email" name="email">
        </div>
        <div class="form_group form_group-email">
            <label for="password">Password:</label>
            <input type="password" name="password">
        </div>
        <input type="submit" value="Log in">
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script type="text/javascript">

    $('#remote_login_form').on('submit', function (e) {
        e.preventDefault();
        $('#success, #error').hide();
        var $theForm = $(this);
        let email = $('input[name="email"]').val();
        let password = $('input[name="password"]').val();

        $.ajax({
            url: "/remote_log_in",
            type: "POST",
            data: {
                "_token": "{{ csrf_token() }}",
                email: email,
                password: password,
                redirect_to: $theForm.attr('action'),
            },
            success: function (response) {
                if (response) {
                    var response_data = $.parseJSON(response);
                    console.log('Success:' + response_data.success);
                    console.log('Message:' + response_data.message);
                    if (response_data.success == true) {
                        $('#success').text(response_data.message).fadeIn(300).delay(1000);
                        window.location.href = "/user";
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
