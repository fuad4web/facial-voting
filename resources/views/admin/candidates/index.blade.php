@extends('admin.layouts.admin')

@section('title', 'Candidates')
@section('header', 'Manage Candidates')

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <div>
            <form method="GET" class="flex space-x-2">
                <select name="category_id" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
        <a href="{{ route('admin.candidates.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Add New Candidate
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Photo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Party</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($candidates as $candidate)
                <tr>
                    <td class="px-6 py-4">
                        @if($candidate->photo)
                            <img src="{{ asset('storage/' . $candidate->photo) }}" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-xs text-gray-500">No</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $candidate->name }}</td>
                    <td class="px-6 py-4">{{ $candidate->party }}</td>
                    <td class="px-6 py-4">{{ $candidate->category->name }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.candidates.edit', $candidate) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                        <form action="{{ route('admin.candidates.destroy', $candidate) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure? This will also delete all votes for this candidate.')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">
            {{ $candidates->links() }}
        </div>
    </div>
@endsection
