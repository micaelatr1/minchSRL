@extends('errors::layout')

@section('title', 'Acceso denegado')
@section('code', '403')
@section('icon')
    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
@endsection
@section('message', 'Acceso denegado')
@section('description', 'No tienes permisos suficientes para acceder a esta sección. Contacta al administrador si crees que deberías tener acceso.')
