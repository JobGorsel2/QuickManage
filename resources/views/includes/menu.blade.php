<div class="menu">
    <div class="menu-wrapper">
        <ul>
            <li><a href="/dashboard">Dashboard</a></li>
            <li><a href="/posts">Posts</a></li>
            <li><a href="/pages">Pages</a></li>
            <li><a href="/media">Media</a></li>
            <li><a href="/settings">Settings</a></li>
            <a class="log-out-btn" href="#"  onclick="event.preventDefault();document.getElementById('logout-form').submit();"> Logout </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
            </form>
        </ul>
    </div>
</div>


    