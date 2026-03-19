@extends('layouts.app')

@section('content')
    @include('includes.menu')

    <div class="container-fluid">
        <div class="row">
            <div class="offset-lg-2 col-lg-9">
                <div class="header-container">
                    <h2 class="m-0">App Gallery</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="offset-lg-2 col-lg-9 col-md-12">
                <div class="body-container">
                    <div class="create-item">
                        <a href="/app-gallery/create"><i class="fa-solid fa-plus"></i> Categorie aanmaken</a>
                    </div>
                   
                     <div class="container">
                        <div class="row">
                                @foreach($app_categories as $category)
                                <div class="col-lg-4">
                                    <div class="app-category-card">
                                        <a href="/app-gallery/category/{{$category->id}}">
                                            <div class="app-category-card-body">
                                                {{$category->category_name}} 
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
@endsection
