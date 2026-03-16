@extends('layouts.voting-app')

@section('title', 'Test Facial Recognition')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">Test Facial Recognition</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Your Registered Face</h3>
                        @if(Auth::user()->facial_image)
                            <img src="{{ Auth::user()->facial_image }}" class="rounded-lg shadow-lg max-w-full">
                        @else
                            <p class="text-red-500">No facial data registered</p>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-2">Facial Descriptors</h3>
                        @if(Auth::user()->facial_descriptors)
                            <p class="text-sm text-gray-600 mb-2">Face data stored: Yes</p>
                            <p class="text-sm text-gray-600">Descriptor length: {{ strlen(Auth::user()->facial_descriptors) }} characters</p>
                            <p class="text-sm text-gray-600 mt-4">Voter ID: {{ Auth::user()->voter_id }}</p>
                        @else
                            <p class="text-red-500">No facial descriptors stored</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
