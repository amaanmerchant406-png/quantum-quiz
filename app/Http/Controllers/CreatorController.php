<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreatorController extends Controller
{
    public function index()
    {
        // Creators can view their quizzes. Users with role 'user' will be redirected or we let them access it if they want to create (we can restrict it via middleware, but let's make it available to any logged-in user who wants to try creating, which is great for portfolio projects, but we will make it show "My Quizzes").
        $quizzes = Quiz::where('creator_id', Auth::id())->withCount('questions')->latest()->get();
        return view('creator.index', compact('quizzes'));
    }

    public function create()
    {
        return view('creator.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|string|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:5|max:300',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit,
            'creator_id' => Auth::id(),
        ]);

        // Automatically upgrade role to creator if they were a user
        $user = Auth::user();
        if ($user->role === 'user') {
            $user->update(['role' => 'creator']);
        }

        return redirect()->route('creator.edit', $quiz->id)->with('success', 'Quiz created successfully! Now add some questions.');
    }

    public function edit(Quiz $quiz)
    {
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $quiz->load('questions.options');
        return view('creator.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:255',
            'difficulty' => 'required|string|in:easy,medium,hard',
            'time_limit' => 'required|integer|min:5|max:300',
        ]);

        $quiz->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'time_limit' => $request->time_limit,
        ]);

        return redirect()->route('creator.edit', $quiz->id)->with('success', 'Quiz details updated!');
    }

    public function destroy(Quiz $quiz)
    {
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $quiz->delete();
        return redirect()->route('creator.index')->with('success', 'Quiz deleted successfully.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1|max:100',
            'options' => 'required|array|min:2|max:6',
            'options.*' => 'required|string',
            'correct_option' => 'required|integer', // index of options array (0-based)
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $request->question_text,
            'points' => $request->points,
        ]);

        foreach ($request->options as $index => $optionText) {
            Option::create([
                'question_id' => $question->id,
                'option_text' => $optionText,
                'is_correct' => ($index == $request->correct_option),
            ]);
        }

        return redirect()->route('creator.edit', $quiz->id)->with('success', 'Question added successfully.');
    }

    public function deleteQuestion(Question $question)
    {
        $quiz = $question->quiz;
        if ($quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $question->delete();
        return redirect()->route('creator.edit', $quiz->id)->with('success', 'Question deleted.');
    }
}
