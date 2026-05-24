@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @include('users._form', ['user' => $user, 'roles' => $roles])
        </div>
    </div>
@stop
