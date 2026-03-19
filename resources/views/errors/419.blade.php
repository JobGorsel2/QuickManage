@extends('errors.errors-layout')

 
@section('title', 'Sessie verlopen')

@section('badge', 'Sessie verlopen')

@section('message')
    Uw sessie is verlopen door inactiviteit. Dit is een beveiligingsmaatregel om uw account te beschermen.<br><br>
    Klik op de knop hieronder om opnieuw in te loggen.
@endsection

@section('button')
<a href="{{ route('login') }}" class="btn ">Opnieuw inloggen</a>
@endsection

@section('footer', 'Als u bezig was met een formulier, moet u uw wijzigingen mogelijk opnieuw invoeren.')