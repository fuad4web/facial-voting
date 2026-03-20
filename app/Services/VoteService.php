<?php

namespace App\Services;

use App\Repositories\Contracts\VoteRepositoryInterface;

class VoteService
{
    protected $voteRepository;
    /**
     * Create a new class instance.
     */
    public function __construct(VoteRepositoryInterface $voteRepository)
    {
        $this->voteRepository = $voteRepository;
    }

    public function createVote(array $data) {
        return $this->voteRepository?->create($data);
    }

    public function fetchVoteResults($category) {
        return $this->voteRepository?->candidateVoteResult($category);
    }

    public function recentVotes() {
        return $this->voteRepository?->recentVotes();
    }

    // count votes
    public function countVoters()
    {
        $countVoters = $this->voteRepository->countVoters();
        return $countVoters ?: 0;
    }

    public function voteCategoryId(int $userId) {
        return $this->voteRepository?->voteCategoryId($userId);
    }

    public function checkUserVoteCategory(int $userId, int $categoryId) {
        return $this->voteRepository?->checkUserVoteCategory($userId, $categoryId);
    }
}
