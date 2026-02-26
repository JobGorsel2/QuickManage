<!-- @extends('layouts.app')

@section('content')
    @include('includes.menu')

    <div class="container-fluid">
        <div class="row">
            <div class="offset-lg-2 col-lg-9">
                <div class="header-container">
                    <h2 class="m-0">Overzicht HTML Pagina's</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="offset-lg-2 col-lg-9 col-md-12">
                <div class="body-container">
                    <div class="create-item">
                        <a href="/pages/create">Pagina aanmaken</a>
                    </div>
                    <div class="table-wrapper">
                        <header>
                            <div class="wr-col-th"><strong>ID</strong></div>
                            <div class="wr-col-th"><strong>Titel</strong></div>
                            <div class="wr-col-th"><strong>URL</strong></div>
                            <div class="wr-col-th"><strong>Aangemaakt door</strong></div>
                            <div class="wr-col-th"><strong>Datum</strong></div>
                            <div class="wr-col-th"><strong>Acties</strong></div>
                        </header>
                        @foreach($pages as $page)
                        <div class="wr-row">
                            <div class="wr-col">{{$page->id}}</div>
                            <div class="wr-col">{{$page->name}}</div>
                            <div class="wr-col"><a href="/pages/view/{{$page->hash_id}}">127.0.0.1:8000/page/view/{{substr($page->hash_id, 0, 7) . '...' }}</a></div>
                            <div class="wr-col">{{$page->author}}</div>
                            <div class="wr-col">{{$page->created_at}}</div>
                            <div class="wr-col-act"><a href="/pages/view/{{$page->hash_id}}"><i class="fa-solid fa-eye eye"></i></a> <a href="/page/{{$page->id}}"><i class="fa-solid fa-pencil pencil"></i></a> <form action="/pages/delete/{{$page->id}}" method="POST"> @csrf  @method('DELETE')<a href="/pages/delete/{{$page->id}}"><i class="fa-solid fa-trash trash"></i></a></form> </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="offset-lg-2 col-lg-9">
                        @if ($message = Session::get('success'))
                        <div class="successMessage alert alert-success alert-block pt-3">  
                            <strong>{{ $message }}</strong>
                        </div>
                        @endif

                        @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-block pt-3">
                            <strong>{{ $error }}</strong>
                            </div>
                        @endforeach
                        @endif
            </div>
        </div>
    </div> 
@endsection -->
