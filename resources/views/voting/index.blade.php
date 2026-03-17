@extends('layouts.voting-app')

@section('title', 'Voting Categories')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6">Voting Categories</h2>
                
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($categories as $category)
                        <div class="border rounded-lg p-6 hover:shadow-lg transition">
                            <h3 class="text-xl font-semibold mb-2">{{ $category->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $category->description }}</p>
                            
                            @if(in_array($category->id, $votes))
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Voted ✓</span>
                            @else
                                <a href="{{ route('voting.show', $category) }}" 
                                   class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                                    Vote Now
                                </a>
                            @endif
                            
                            <a href="{{ route('voting.results', $category) }}" class="ml-2 text-indigo-600 hover:underline">View Results</a>
                        </div>
                    @empty
                        <p class="text-gray-500">No active categories at the moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
