@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @include('users._form', ['user' => null, 'roles' => $roles])
        </div>
    </div>
@stop
