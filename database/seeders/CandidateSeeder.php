<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidate;
use App\Models\Category;

class CandidateSeeder extends Seeder
{
    public function run()
    {
        // President candidates
        $presidentCategory = Category::where('name', 'President')->first();
        if ($presidentCategory) {
            Candidate::create([
                'category_id' => $presidentCategory->id,
                'name' => 'Abdulhammed Fuad',
                'party' => 'All Progressive Party',
                'bio' => 'Experienced leader with 20 years in public service.'
            ]);
            Candidate::create([
                'category_id' => $presidentCategory->id,
                'name' => 'Oseni Luthfulahi',
                'party' => 'Unity Party',
                'bio' => 'Advocate for education and healthcare reform.'
            ]);
        }

        // Vice President candidates
        $vpCategory = Category::where('name', 'Vice President')->first();
        if ($vpCategory) {
            Candidate::create([
                'category_id' => $vpCategory->id,
                'name' => 'Subair Ridwan',
                'party' => 'Progressive Party',
                'bio' => 'Former governor with strong economic record.'
            ]);
            Candidate::create([
                'category_id' => $vpCategory->id,
                'name' => 'Okanlawon Pelumi',
                'party' => 'Unity Party',
                'bio' => 'Human rights lawyer and activist.'
            ]);
        }

        // Senator candidates
        $senatorCategory = Category::where('name', 'Senator')->first();
        if ($senatorCategory) {
            Candidate::create([
                'category_id' => $senatorCategory->id,
                'name' => 'Subair Ridwan',
                'party' => 'Progressive Party',
                'bio' => 'Former governor with strong economic record.'
            ]);
            Candidate::create([
                'category_id' => $senatorCategory->id,
                'name' => 'Okanlawon Pelumi',
                'party' => 'Unity Party',
                'bio' => 'Human rights lawyer and activist.'
            ]);
        }

        // governor candidates
        $governorCategory = Category::where('name', 'Governor')->first();
        if ($governorCategory) {
            Candidate::create([
                'category_id' => $governorCategory->id,
                'name' => 'Abdulhammed Fuad',
                'party' => 'All Progressive Party',
                'bio' => 'Experienced leader with 20 years in public service.'
            ]);
            Candidate::create([
                'category_id' => $governorCategory->id,
                'name' => 'Oseni Luthfulahi',
                'party' => 'Unity Party',
                'bio' => 'Advocate for education and healthcare reform.'
            ]);
        }

        // mayor candidates
        $mayorCategory = Category::where('name', 'Mayor')->first();
        if ($mayorCategory) {
            Candidate::create([
                'category_id' => $mayorCategory->id,
                'name' => 'Subair Ridwan',
                'party' => 'Progressive Party',
                'bio' => 'Former governor with strong economic record.'
            ]);
            Candidate::create([
                'category_id' => $mayorCategory->id,
                'name' => 'Okanlawon Pelumi',
                'party' => 'Unity Party',
                'bio' => 'Human rights lawyer and activist.'
            ]);
        }
    }
}
