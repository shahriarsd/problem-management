<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProblemRequest;
use App\Http\Requests\UpdateProblemRequest;
use App\Models\Problem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProblemController extends Controller
{
    /**
     * Display a paginated, searchable and sortable listing of the problems.
     */
    public function index(Request $request): View
    {
        $sortable = [
            'id' => 'id',
            'created' => 'created_at',
            'edited' => 'updated_at',
        ];

        $sort = (string) $request->query('sort', 'created');
        $direction = strtolower((string) $request->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';
        $search = trim((string) $request->query('search', ''));

        $problems = Problem::query()
            ->when($search !== '', function ($query) use ($search) {
                $term = '%'.addcslashes($search, '%_\\').'%';

                $query->where(function ($query) use ($search, $term) {
                    $query->where('title', 'like', $term);

                    if (ctype_digit($search)) {
                        $query->orWhere('id', (int) $search);
                    }
                });
            })
            ->orderBy($sortable[$sort] ?? 'created_at', $direction)
            ->paginate(10)
            ->withQueryString();

        return view('problems.index', compact('problems', 'sort', 'direction', 'search'));
    }

    /**
     * Show the form for creating a new problem.
     */
    public function create(): View
    {
        return view('problems.create');
    }

    /**
     * Store a newly created problem in storage.
     */
    public function store(StoreProblemRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['image_path'] = $request->file('image')->store('problems', 'public');

        Problem::create($validated);

        return redirect()
            ->route('problems.index')
            ->with('success', 'The problem and its solution were created successfully.');
    }

    /**
     * Display the specified problem.
     */
    public function show(Problem $problem): View
    {
        return view('problems.show', compact('problem'));
    }

    /**
     * Show the form for editing the specified problem.
     */
    public function edit(Problem $problem): View
    {
        return view('problems.edit', compact('problem'));
    }

    /**
     * Update the specified problem in storage.
     */
    public function update(UpdateProblemRequest $request, Problem $problem): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('problems', 'public');

            if ($problem->image_path) {
                Storage::disk('public')->delete($problem->image_path);
            }

            $validated['image_path'] = $newImagePath;
        }

        $problem->update($validated);

        return redirect()
            ->route('problems.index')
            ->with('success', 'The problem and its solution were updated successfully.');
    }

    /**
     * Remove the specified problem from storage.
     */
    public function destroy(Problem $problem): RedirectResponse
    {
        if ($problem->image_path) {
            Storage::disk('public')->delete($problem->image_path);
        }

        $problem->delete();

        return redirect()
            ->route('problems.index')
            ->with('success', 'The record was deleted successfully.');
    }
}
