@extends('layouts.app')

@section('content')
    <div class="menu">
        <div class="menu-wrapper">
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="#">Posts</a></li>
                <li><a href="#">Pages</a></li>
                <li><a href="#">Media</a></li>
                <li><a href="#">Settings</a></li>
                <a class="log-out-btn" href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit();"> Logout </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                </form>
            </ul>
        </div>
    </div>
@endsection



