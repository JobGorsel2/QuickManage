@extends('errors.errors-layout')

@section('code',__('503'))

@section('title',__('Service Unavailable'))

@section('badge',__('No Database Connection'))

@section('message')
   Er kan momenteel geen verbinding worden gemaakt met de database. Dit is meestal een tijdelijk probleem.<br> <br><br/>
   Probeer het later <a href="{{ url()->current() }}" >opnieuw</a>.

@endsection
{{-- 
@section('button')
<a href="{{ url()->current() }}" class="btn">Opnieuw proberen</a>
@endsection --}}

@section('footer',__(' Mocht dit probleem zich blijven voordoen, neem dan contact op met de systeembeheerder.'))
    

 
