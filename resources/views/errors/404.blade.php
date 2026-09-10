@extends('errors::layout')

@section('title', 'Página no encontrada')
@section('code', '404')
@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
@endsection
@section('message', 'Página no encontrada')
@section('description', 'La página que buscas no existe o ha sido movida. Revisa la URL o navega desde el inicio.')
