@extends('layouts.master')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Pengajuan Izin</h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('siswa.izin.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="tanggal_izin" class="block font-medium text-sm text-gray-700">Tanggal Izin</label>
                            <input type="date" name="tanggal_izin" id="tanggal_izin"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                value="{{ old('tanggal_izin') }}" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            @error('tanggal_izin')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="alasan" class="block font-medium text-sm text-gray-700">Alasan</label>
                            <textarea name="alasan" id="alasan" rows="5"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('alasan') }}</textarea>
                            @error('alasan')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('siswa.izin.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
