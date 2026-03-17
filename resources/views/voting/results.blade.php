@extends('layouts.voting-app')

@section('title', 'Election Results - ' . $category->name)
@section('header', $category->name . ' - Live Results')

@section('content')
<div class="py-12" 
     x-data="resultsHandler()" 
     x-init="init()"
     data-category-id="{{ $category->id }}"
     data-initial-candidates='@json($candidates)'>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">{{ $category->name }} Results</h2>
                <p class="text-gray-600 mb-8">{{ $category->description }}</p>

                <!-- Live Stats -->
                <div class="mb-6">
                    <span class="text-sm text-gray-500">Total Votes: <span x-text="totalVotes"></span></span>
                </div>

                <!-- Candidates List -->
                <div class="space-y-6">
                    <template x-for="candidate in candidates" :key="candidate.id">
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold" x-text="candidate.name"></h3>
                                    <p class="text-sm text-gray-600" x-text="candidate.party"></p>
                                </div>
                                <span class="text-2xl font-bold text-indigo-600" x-text="candidate.votes_count"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                                     :style="'width: ' + (candidate.votes_count / totalVotes * 100 || 0) + '%'">
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1" x-text="`${((candidate.votes_count / totalVotes * 100) || 0).toFixed(1)}% of total`"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function resultsHandler() {
    return {
        candidates: [],
        totalVotes: 0,
        categoryId: null,
        init() {
            // Get initial data from data attributes
            this.categoryId = this.$el.dataset.categoryId;
            this.candidates = JSON.parse(this.$el.dataset.initialCandidates);
            this.calcTotal();

            // Listen for real-time updates via Echo
            if (window.Echo) {
                window.Echo.channel(`category.${this.categoryId}`)
                    .listen('.vote.cast', (e) => {
                        // Increment vote count for the candidate
                        const candidate = this.candidates.find(c => c.id === e.candidate_id);
                        if (candidate) {
                            candidate.votes_count++;
                            this.calcTotal();
                        }
                    });
            } else {
                console.warn('Echo not initialized; real-time updates disabled.');
            }
        },
        calcTotal() {
            this.totalVotes = this.candidates.reduce((sum, c) => sum + c.votes_count, 0);
        }
    }
}
</script>
@endpush
@endsection
