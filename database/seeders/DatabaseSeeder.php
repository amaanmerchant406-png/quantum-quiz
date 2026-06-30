<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Option;
use App\Models\QuizAttempt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        $creator = User::create([
            'name' => 'Quantum Master',
            'email' => 'admin@quantum.com',
            'password' => Hash::make('admin123'),
            'role' => 'creator',
            'xp' => 1250,
            'level' => 5,
        ]);

        $explorer = User::create([
            'name' => 'Quiz Explorer',
            'email' => 'user@quantum.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'xp' => 450,
            'level' => 2,
        ]);

        $competitor1 = User::create([
            'name' => 'Aria Vane',
            'email' => 'aria@quantum.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'xp' => 950,
            'level' => 4,
        ]);

        $competitor2 = User::create([
            'name' => 'Neo Archer',
            'email' => 'neo@quantum.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'xp' => 780,
            'level' => 3,
        ]);

        // 2. Create Quizzes
        // QUIZ 1: Science & Cosmos
        $quiz1 = Quiz::create([
            'title' => 'Cosmic Anomalies',
            'description' => 'Embark on a voyage across space-time, investigating black hole mechanics, quantum particles, and the age of our cosmos.',
            'category' => 'Science & Cosmos',
            'difficulty' => 'medium',
            'time_limit' => 20, // 20s per question
            'creator_id' => $creator->id,
        ]);

        $q1_1 = Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'What is the approximate age of the observable universe?',
            'points' => 15,
        ]);
        Option::create(['question_id' => $q1_1->id, 'option_text' => '13.8 billion years', 'is_correct' => true]);
        Option::create(['question_id' => $q1_1->id, 'option_text' => '4.5 billion years', 'is_correct' => false]);
        Option::create(['question_id' => $q1_1->id, 'option_text' => '9.3 billion years', 'is_correct' => false]);
        Option::create(['question_id' => $q1_1->id, 'option_text' => '20.1 billion years', 'is_correct' => false]);

        $q1_2 = Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'Which subatomic particles are responsible for holding quarks together inside protons and neutrons?',
            'points' => 15,
        ]);
        Option::create(['question_id' => $q1_2->id, 'option_text' => 'Gluons', 'is_correct' => true]);
        Option::create(['question_id' => $q1_2->id, 'option_text' => 'Photons', 'is_correct' => false]);
        Option::create(['question_id' => $q1_2->id, 'option_text' => 'Bosons', 'is_correct' => false]);
        Option::create(['question_id' => $q1_2->id, 'option_text' => 'Gravitons', 'is_correct' => false]);

        $q1_3 = Question::create([
            'quiz_id' => $quiz1->id,
            'question_text' => 'What is the boundary surrounding a black hole from which nothing, not even light, can escape?',
            'points' => 20,
        ]);
        Option::create(['question_id' => $q1_3->id, 'option_text' => 'Event Horizon', 'is_correct' => true]);
        Option::create(['question_id' => $q1_3->id, 'option_text' => 'Singularity', 'is_correct' => false]);
        Option::create(['question_id' => $q1_3->id, 'option_text' => 'Ergosphere', 'is_correct' => false]);
        Option::create(['question_id' => $q1_3->id, 'option_text' => 'Accretion Disk', 'is_correct' => false]);


        // QUIZ 2: Technology & Programming
        $quiz2 = Quiz::create([
            'title' => 'Web Quantum Mechanics',
            'description' => 'Test your core mastery of web security, data structures, and advanced execution runtimes in modern web architecture.',
            'category' => 'Technology & Programming',
            'difficulty' => 'hard',
            'time_limit' => 25, // 25s per question
            'creator_id' => $creator->id,
        ]);

        $q2_1 = Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Which HTTP header provides protection against clickjacking by preventing the page from loading in iframe elements?',
            'points' => 20,
        ]);
        Option::create(['question_id' => $q2_1->id, 'option_text' => 'X-Frame-Options', 'is_correct' => true]);
        Option::create(['question_id' => $q2_1->id, 'option_text' => 'Content-Security-Policy', 'is_correct' => false]);
        Option::create(['question_id' => $q2_1->id, 'option_text' => 'Strict-Transport-Security', 'is_correct' => false]);
        Option::create(['question_id' => $q2_1->id, 'option_text' => 'X-Content-Type-Options', 'is_correct' => false]);

        $q2_2 = Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'What is the worst-case search time complexity of a value in a standard, unbalanced Binary Search Tree (BST)?',
            'points' => 20,
        ]);
        Option::create(['question_id' => $q2_2->id, 'option_text' => 'O(n)', 'is_correct' => true]);
        Option::create(['question_id' => $q2_2->id, 'option_text' => 'O(log n)', 'is_correct' => false]);
        Option::create(['question_id' => $q2_2->id, 'option_text' => 'O(1)', 'is_correct' => false]);
        Option::create(['question_id' => $q2_2->id, 'option_text' => 'O(n log n)', 'is_correct' => false]);

        $q2_3 = Question::create([
            'quiz_id' => $quiz2->id,
            'question_text' => 'Which PHP magic method is triggered when an object instance is called directly as a function?',
            'points' => 20,
        ]);
        Option::create(['question_id' => $q2_3->id, 'option_text' => '__invoke', 'is_correct' => true]);
        Option::create(['question_id' => $q2_3->id, 'option_text' => '__call', 'is_correct' => false]);
        Option::create(['question_id' => $q2_3->id, 'option_text' => '__toString', 'is_correct' => false]);
        Option::create(['question_id' => $q2_3->id, 'option_text' => '__construct', 'is_correct' => false]);


        // QUIZ 3: History & Geography
        $quiz3 = Quiz::create([
            'title' => 'Epochs & Empires',
            'description' => 'Traverse ancient ruins and navigate historical capitals that shaped global borders.',
            'category' => 'History & Geography',
            'difficulty' => 'easy',
            'time_limit' => 15,
            'creator_id' => $creator->id,
        ]);

        $q3_1 = Question::create([
            'quiz_id' => $quiz3->id,
            'question_text' => 'Which pre-Columbian civilization built the mountain citadel of Machu Picchu in Peru?',
            'points' => 10,
        ]);
        Option::create(['question_id' => $q3_1->id, 'option_text' => 'Incas', 'is_correct' => true]);
        Option::create(['question_id' => $q3_1->id, 'option_text' => 'Mayans', 'is_correct' => false]);
        Option::create(['question_id' => $q3_1->id, 'option_text' => 'Aztecs', 'is_correct' => false]);
        Option::create(['question_id' => $q3_1->id, 'option_text' => 'Olmecs', 'is_correct' => false]);

        $q3_2 = Question::create([
            'quiz_id' => $quiz3->id,
            'question_text' => 'What was the capital of the Eastern Roman Empire, also known as the Byzantine Empire?',
            'points' => 10,
        ]);
        Option::create(['question_id' => $q3_2->id, 'option_text' => 'Constantinople', 'is_correct' => true]);
        Option::create(['question_id' => $q3_2->id, 'option_text' => 'Rome', 'is_correct' => false]);
        Option::create(['question_id' => $q3_2->id, 'option_text' => 'Athens', 'is_correct' => false]);
        Option::create(['question_id' => $q3_2->id, 'option_text' => 'Alexandria', 'is_correct' => false]);

        // 3. Create Quiz Attempts (History & Leaderboards)
        QuizAttempt::create([
            'user_id' => $competitor1->id,
            'quiz_id' => $quiz1->id,
            'score' => 50,
            'total_questions' => 3,
            'correct_answers' => 3,
            'time_taken' => 35,
            'completed_at' => now()->subHours(2),
        ]);

        QuizAttempt::create([
            'user_id' => $competitor2->id,
            'quiz_id' => $quiz1->id,
            'score' => 30,
            'total_questions' => 3,
            'correct_answers' => 2,
            'time_taken' => 45,
            'completed_at' => now()->subHours(1),
        ]);

        QuizAttempt::create([
            'user_id' => $competitor1->id,
            'quiz_id' => $quiz2->id,
            'score' => 40,
            'total_questions' => 3,
            'correct_answers' => 2,
            'time_taken' => 50,
            'completed_at' => now()->subMinutes(30),
        ]);

        QuizAttempt::create([
            'user_id' => $explorer->id,
            'quiz_id' => $quiz3->id,
            'score' => 20,
            'total_questions' => 2,
            'correct_answers' => 2,
            'time_taken' => 18,
            'completed_at' => now()->subMinutes(5),
        ]);
    }
}
