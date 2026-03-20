@extends('layouts.app')

@section('content')
    @include('includes.menu')

        <div class="container-fluid">
            <div class="message_block">
                <div class="offset-lg-2 col-lg-9">
                    @if ($message = Session::get('success'))
                        <div id="successMessage" class="successMessage alert alert-success alert-block pt-3 text-center">  
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                    @if ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-block pt-3 text-center">
                                <strong>{{ $error }}</strong>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        <div class="row">
            <div class="offset-lg-2 col-lg-9">

                <div class="header-container">

                    <h2 class="m-0" id="categoryTitle" data-id="{{ $app_category->id }}">Overzicht categorie: <span id="categoryName">{{ $app_category->category_name }}</span></h2>
                 
                    <div class="actions-wrapper">
                        <i id="editIcon" class="fa-solid fa-pen-to-square " style="cursor:pointer;"></i>
                        <i id="saveIcon" class="fa-solid fa-check  " style="cursor:pointer; display:none; color:green;"></i>
                        <i id="cancelIcon" class="fa-solid fa-xmark " style="cursor:pointer; display:none; color:red;"></i> 
                        <form action="/app-gallery/category/delete/{{$app_category->id}}" method="POST">

                            @csrf  @method('DELETE')

                            <button type="submit" class="btn_delete"   >
                                <i class="fa-solid fa-trash trash"></i>
                            </button>

                        </form> 
                    </div>
                </div>
                
            </div>
        </div>
        <div class="row">
            <div class="offset-lg-2 col-lg-9 col-md-12">
                <div class="body-container">
                    <div class="create-item">
                        <a href="/pages/create">App toevoegen</a>
                    </div>

                     <div class="container">
                        <div class="row">
                                @foreach($apps as $app)
                                <div class="col-lg-4">
                                    <div class="app-category-card">
                                        <a href="/pages/edit/{{$app->id}}">
                                            <div class="app-category-card-body">
                                                {{$app->name}} 
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                        </div>
                     </div>
                    
                </div>
            </div>
        </div>
         
    </div> 
@endsection
 