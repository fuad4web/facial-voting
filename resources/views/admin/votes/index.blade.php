@extends('admin.layouts.admin')

@section('title', 'Votes & Results')
@section('header', 'Votes & Results')

@section('content')
    <!-- Results by Category -->
    <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold mb-6">Election Results</h3>
        
        @foreach($results as $result)
            <div class="mb-8 last:mb-0">
                <h4 class="font-medium text-gray-800 mb-3 text-lg border-b pb-2">{{ $result['category']->name }}</h4>
                <div class="space-y-4">
                    @foreach($result['candidates'] as $candidate)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>
                                    <span class="font-medium">{{ $candidate->name }}</span>
                                    @if($candidate->party)
                                        <span class="text-gray-500 text-xs ml-2">({{ $candidate->party }})</span>
                                    @endif
                                </span>
                                <span class="font-medium">{{ $candidate->votes_count }} votes</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                @php $percent = $result['total'] > 0 ? round(($candidate->votes_count / $result['total']) * 100) : 0; @endphp
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $percent }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $percent }}% of total</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <!-- All Votes List -->
    <div class="bg-white shadow-sm rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4">All Votes</h3>
        
        <div class="mb-4">
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
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Voter</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Candidate</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($votes as $vote)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vote->user->name }} ({{ $vote->user->voter_id }})</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vote->candidate->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vote->category->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $vote->voted_at->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $votes->links() }}
            </div>
        </div>
    </div>
@endsection
