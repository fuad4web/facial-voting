<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $query = Candidate::with('category');
        
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        $candidates = $query->orderBy('name')->paginate(15);
        $categories = Category::orderBy('order')->get();
        
        return view('admin.candidates.index', compact('candidates', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('order')->get();
        return view('admin.candidates.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'party' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['category_id', 'name', 'party', 'bio']);
        
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('candidates', 'public');
            $data['photo'] = $path;
        }

        Candidate::create($data);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate created.');
    }

    public function edit(Candidate $candidate)
    {
        $categories = Category::orderBy('order')->get();
        return view('admin.candidates.edit', compact('candidate', 'categories'));
    }

    public function update(Request $request, Candidate $candidate)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'party' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['category_id', 'name', 'party', 'bio']);
        
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }
            $path = $request->file('photo')->store('candidates', 'public');
            $data['photo'] = $path;
        }

        $candidate->update($data);

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate updated.');
    }

    public function destroy(Candidate $candidate)
    {
        if ($candidate->votes()->exists()) {
            return back()->with('error', 'Cannot delete candidate with votes.');
        }
        
        if ($candidate->photo) {
            Storage::disk('public')->delete($candidate->photo);
        }
        
        $candidate->delete();

        return redirect()->route('admin.candidates.index')
            ->with('success', 'Candidate deleted.');
    }
}
