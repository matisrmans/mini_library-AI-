<?php

namespace App\Http\Controllers;

use App\Models\Reader;
use Illuminate\Http\Request;

class ReaderController extends Controller
{
    public function index()
    {
        $readers = Reader::latest()->paginate(10);
        return view('readers.index', compact('readers'));
    }

    public function create()
    {
        return view('readers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:readers,email',
        ]);

        Reader::create($validated);

        return redirect()->route('readers.index')->with('success', 'Lasītājs pievienots!');
    }

    public function show(Reader $reader)
    {
        return view('readers.show', compact('reader'));
    }

    public function edit(Reader $reader)
    {
        return view('readers.edit', compact('reader'));
    }

    public function update(Request $request, Reader $reader)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:readers,email,' . $reader->id,
        ]);

        $reader->update($validated);

        return redirect()->route('readers.index')->with('success', 'Lasītājs atjaunināts!');
    }

    public function destroy(Reader $reader)
    {
        $reader->delete();

        return redirect()->route('readers.index')->with('success', 'Lasītājs dzēsts!');
    }
}
