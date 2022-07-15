@include('shared.head')
<div
    class="relative flex items-top justify-center min-h-screen sm:items-center py-4 sm:pt-0">
    <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
            <h1>Your profile</h1>
        </div>
        <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
            <p>
                @if (!empty($user_data['first_name']))
                    <strong>First name: </strong> {{ $user_data['first_name'] }} <br>
                @endif

                @if (!empty($user_data['last_name']))
                    <strong>Last name: </strong> {{ $user_data['last_name'] }} <br>
                @endif
            </p>
        </div>
        <div class="flex justify-center pt-8 sm:justify-start sm:pt-0">
            <a href="/logout" data-auth_action="logout" style="margin: 20px 0; padding: 5px 10px; background-color: grey; ">Log out!</a>
        </div>
    </div>
</div>
@include('shared.footer')
