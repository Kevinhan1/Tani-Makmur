@extends('layouts.main')

@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
    <h2 class="text-2xl font-semibold mb-4">Selamat Datang, {{ session('user')->namapengguna }}!</h2>
@endsection
