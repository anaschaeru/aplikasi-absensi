@extends('layouts.master')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Edit Siswa: {{ $siswa->nama_siswa }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.siswa.update', $siswa->siswa_id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @include('admin.siswa._form', ['siswa' => $siswa])

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.siswa.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
