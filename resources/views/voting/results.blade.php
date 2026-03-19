@extends('layouts.voting-app')

@section('title', 'Election Results - ' . $category->name)
@section('content')
<div class="py-12"
     x-data="resultsPolling()"
     x-init="startPolling()"
     data-category-id="{{ $category->id }}">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-4">{{ $category->name }} - Live Results</h2>
                <p class="text-gray-600 mb-8">{{ $category->description }}</p>

                <!-- Total Votes -->
                <div class="mb-4 text-gray-600">
                    Total Votes: <span class="font-bold" x-text="totalVotes"></span>
                </div>

                <!-- Candidates List -->
                <div class="space-y-6">
                    <template x-for="candidate in candidates" :key="candidate.id">
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <div>
                                    <h3 class="text-lg font-semibold" x-text="candidate.name"></h3>
                                    <p class="text-sm text-gray-600" x-text="candidate.party"></p>
                                </div>
                                <span class="text-2xl font-bold text-indigo-600" x-text="candidate.votes_count"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500"
                                     :style="'width: ' + ((candidate.votes_count / totalVotes * 100) || 0) + '%'">
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1"
                               x-text="((candidate.votes_count / totalVotes * 100) || 0).toFixed(1) + '%'">
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resultsPolling() {
    return {
        candidates: [],
        totalVotes: 0,
        categoryId: null,
        pollingInterval: null,

        startPolling() {
            this.categoryId = this.$el.dataset.categoryId;
            this.fetchResults(); // immediate fetch
            this.pollingInterval = setInterval(() => {
                this.fetchResults();
            }, 3000); // poll every 3 seconds
        },

        fetchResults() {
            fetch(`/voting/${this.categoryId}/results/json`)
                .then(response => response.json())
                .then(data => {
                    this.candidates = data.candidates;
                    this.totalVotes = data.total_votes;
                })
                .catch(error => console.error('Error fetching results:', error));
        },

        // Cleanup interval when Alpine component is destroyed
        destroy() {
            if (this.pollingInterval) {
                clearInterval(this.pollingInterval);
            }
        }
    }
}
</script>
@endsection
