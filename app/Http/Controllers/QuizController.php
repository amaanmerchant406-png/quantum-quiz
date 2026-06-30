<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    public function index(Request $request)
    {
        $query = Quiz::with(['creator', 'questions']);

        // Filtering
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $quizzes = $query->latest()->get();

        // Unique categories list
        $categories = Quiz::select('category')->distinct()->pluck('category');

        // User stats
        $user = Auth::user();
        $userAttemptsCount = QuizAttempt::where('user_id', $user->id)->count();
        $userAverageScore = QuizAttempt::where('user_id', $user->id)->avg('score') ?? 0;
        
        // Calculate global rank
        $rankings = User::orderByDesc('xp')->orderByDesc('id')->pluck('id')->toArray();
        $userRank = array_search($user->id, $rankings) !== false ? array_search($user->id, $rankings) + 1 : count($rankings);

        // Leaderboard
        $leaderboard = User::orderByDesc('xp')->limit(5)->get();

        return view('dashboard', compact('quizzes', 'categories', 'userAttemptsCount', 'userAverageScore', 'userRank', 'leaderboard'));
    }

    public function play(Quiz $quiz)
    {
        // Load questions with options
        $quiz->load(['questions.options']);

        if ($quiz->questions->isEmpty()) {
            return redirect()->route('dashboard')->with('error', 'This quiz has no questions yet.');
        }

        return view('quiz.play', compact('quiz'));
    }

    public function submitAttempt(Request $request, Quiz $quiz)
    {
        $user = Auth::user();
        $answers = $request->input('answers', []); // format: [question_id => option_id]
        $timeTaken = $request->input('time_taken', 0); // in seconds

        $questions = Question::where('quiz_id', $quiz->id)->with('options')->get();
        $correctCount = 0;
        $totalScore = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $question) {
            $selectedOptionId = $answers[$question->id] ?? null;
            $correctOption = $question->options->where('is_correct', true)->first();

            if ($correctOption && $selectedOptionId == $correctOption->id) {
                $correctCount++;
                $totalScore += $question->points;
            }
        }

        // Create Quiz Attempt
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => $totalScore,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctCount,
            'time_taken' => $timeTaken,
            'completed_at' => now(),
        ]);

        // Gamification: Add XP and Level up logic
        $xpEarned = $totalScore; // 1 point = 1 XP
        // Bonus for 100% accuracy
        if ($correctCount === $totalQuestions && $totalQuestions > 0) {
            $xpEarned += 50; // Perfect score bonus
        }

        $newXp = $user->xp + $xpEarned;
        $currentLevel = $user->level;
        
        // Calculate new level: level threshold is level * 500 XP
        // e.g., Level 1 -> 500 XP to reach Level 2. Level 2 -> 1000 XP to reach Level 3.
        // Formula: Accumulative XP for level L is 250 * L * (L - 1). Or simpler: Level Up when XP >= level * 400
        $newLevel = $currentLevel;
        while ($newXp >= ($newLevel * 400)) {
            $newLevel++;
        }

        $user->update([
            'xp' => $newXp,
            'level' => $newLevel,
        ]);

        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'xp_earned' => $xpEarned,
            'level_up' => $newLevel > $currentLevel,
            'new_level' => $newLevel,
            'redirect_url' => route('quiz.attempt', $attempt->id)
        ]);
    }

    public function showAttempt(QuizAttempt $attempt)
    {
        // Security check: only the attempt owner or the creator of the quiz can view this summary
        if ($attempt->user_id !== Auth::id() && $attempt->quiz->creator_id !== Auth::id()) {
            abort(403);
        }

        $attempt->load(['quiz.questions.options', 'user']);

        return view('quiz.attempt', compact('attempt'));
    }

    public function leaderboard()
    {
        $leaderboard = User::orderByDesc('xp')->get();
        return view('leaderboard', compact('leaderboard'));
    }

    public function profile()
    {
        $user = Auth::user();
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->with('quiz')
            ->latest()
            ->get();

        // Calculate stats
        $totalXp = $user->xp;
        $level = $user->level;
        $totalQuizzes = $attempts->count();
        $avgAccuracy = $totalQuizzes > 0 ? ($attempts->sum('correct_answers') / $attempts->sum('total_questions')) * 100 : 0;
        
        return view('profile', compact('user', 'attempts', 'totalXp', 'level', 'totalQuizzes', 'avgAccuracy'));
    }
}
