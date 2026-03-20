<?php
    namespace App\Repositories\Contracts;

use App\Models\Category;
use App\Models\Vote;

    interface VoteRepositoryInterface
    {
        public function create(array $data): Vote;
        public function voteCategoryId(int $userId);
        public function countVoters();
        public function recentVotes();
        public function candidateVoteResult(Category $category);
        public function checkUserVoteCategory(int $userId, int $categoryId);
    }
