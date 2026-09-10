@extends('errors::layout')

@section('title', 'No autorizado')
@section('code', '401')
@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m9.364-7.364A9 9 0 1112 3a9 9 0 017.364 4.636z"/></svg>
@endsection
@section('message', 'No autorizado')
@section('description', 'No tienes las credenciales necesarias para acceder a esta página. Inicia sesión con una cuenta autorizada.')
