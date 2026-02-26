@extends('layouts.app')

@section('content')
    @include('includes.menu')
    <div class="message_block">
        <div class="offset-lg-2 col-lg-9">
            @if ($message = Session::get('success'))
                <div class="successMessage alert alert-success alert-block pt-3 text-center">  
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

    
    <div class="container-fluid">
        <div class="row">
            <div class="offset-lg-2 col-lg-9">
                <div class="header-container">
                    <h2 class="m-0">Pagina: {{ $page->name }} bewerken</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="offset-lg-2 col-lg-9">
                <div class="body-container">
                    <div class="body-header-text">
                        <p>Maak de pagina op met HTML Content:</p>
                    </div>
                    <form action='/pages/update/{{$page->id}}' method="POST">
                        @method('PATCH')
                        @csrf    
                        <p class="c-bold">HTML Pagina naam:</p>
                        <input type="text" name='name' value="{{ $page->name }}" required><br/><br/>
                        <p class="c-bold">Omschrijving:</p> 
                        <textarea name='description' >{{ $page->description }}</textarea><br/><br/>
                        <p class="c-bold">Repository:<br/><br/>

                        <input type="text" name='repo' value='{{ $page->repository }}' disabled><br/><br/>

                        <p class="c-bold">Workspace:</p>

                        <input type="text" name='workspace' value='{{ $page->workspace }}' disabled><br/><br/>

                        <p class="c-bold">Service:</p>

                        <input type="text" name='service' value='{{ $page->service }}' disabled><br/><br/>

                        <p class="c-bold">Template:</p> 
                                 
                        <input type="text" name='template' value='{{ $page->template->name }}' disabled ><br/><br/>
       

                        {{-- <div class="workspace_parameters_title"><div class="c-bold">Workspace parameters</div><div class="show_in_app c-bold">Laten zien in App</div></div>  --}}
                            

                        {{-- <textarea class="textarea-content-page" name="content" placeholder="Content van de pagina..." required>{{$page->content}}</textarea><br/><br> 
                            <div  type="text" id='addfield' onclick=addField()><i class="fa fa-plus"></i></div> 
                                <div  type="text" id='removefield' onclick=removeField()><i class="fa fa-minus"></i></div>
                            <div id="formfield"></div>
                        <br> --}}
                        <input type="submit" name="submit" value='Opslaan'>
                    </form>
                </div>
                        
            </div>
        </div>
    </div> 
    
    
@endsection