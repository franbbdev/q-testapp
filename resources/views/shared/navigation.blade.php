<header style="position: relative; width: 100%; height: 100px; background: #4a5568">
    <nav style="position:relative; display: block; width: 100%; overflow: hidden; padding: 10px;">
        <ul style="list-style: none; float: right; padding: 0; margin: 0;">
            @if(Session::has('access_token'))
                <li style="float: left; padding-left: 30px; color: white;"><a href="/logout" data-auth_action="logout">Log out</a></li>
            @else
                <li style="float: left; padding-left: 30px; color: white;"><a href="/login">Log in</a></li>
            @endif
            <li style="float: left; padding-left: 30px; color: white;"><a href="/user">Profile</a></li>
        </ul>
    </nav>
    <nav style="position:relative; display: block; width: 100%; overflow: hidden; padding: 10px;">
        <ul style="list-style: none; float: left; padding: 0; margin: 0;">
            <li style="float: left; padding-right: 30px; color: white;"><a href="/add-book">Add book</a></li>
            <li style="float: left; padding-right: 30px; color: white;"><a href="/authors">Authors</a></li>
        </ul>
    </nav>
</header>

