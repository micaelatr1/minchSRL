@extends('errors::layout')

@section('title', 'Sesión expirada')
@section('code', '419')
@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
@endsection
@section('message', 'Sesión expirada')
@section('description', 'Tu sesión ha caducado por inactividad. Inicia sesión nuevamente para continuar.')
