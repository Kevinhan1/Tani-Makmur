@extends('layouts.main')

@section('title', 'Dashboard')
@section('page', 'Dashboard')

@section('content')
    <h2 class="text-2xl font-semibold mb-3">Selamat Datang, {{ session('user')->namapengguna }}!</h2>

    <h2 class="text-2xl font-semibold mt-4 mb-4">Penjualan Hari ini</h2>

    <div class="flex gap-[15px]">
        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Total Penjualan</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">Rp5000.000</p>
        </div>

        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Transaksi</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">24</p>
        </div>

        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Produk terjual</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">200</p>
        </div>
    </div>

    <h2 class="text-2xl font-semibold mt-3 mb-4">Penjualan Bulanan ini</h2>
        <div class="flex gap-[15px]">
        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Total Penjualan</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">Rp100.000.000</p>
        </div>

        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Transaksi</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">60  </p>
        </div>

        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Produk terjual</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">200</p>
        </div>
    </div>

    <h2 class="text-2xl font-semibold mt-3 mb-4">Produk</h2>

    <div class="flex gap-[15px]">
        <div class="bg-white p-6 rounded shadow" style="height: 150px; width: 400px;">
            <div class="flex justify-between items-center mb-6">
                <p style="font-size: 20px;" class="font-medium">Produk Terlaris</p>
            </div>
            <p style="font-size: 24px;" class="font-medium">Pupuk NPK / 10 kg zak </p>
        </div>
    </div>  
@endsection
