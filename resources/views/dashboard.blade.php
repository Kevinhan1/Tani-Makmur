@extends('layouts.main')

@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
    <p class="text-xl">Selamat datang, {{ session('user')->namapengguna }}!</p>
@endsection
