<?php

namespace App\Http\Controllers\Student\Subject;

use App\Models\Question;
use App\Models\TestResult;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('student.Test.test');
    }

    public function subjectTest()
    {
        $data['questions'] = Question::inRandomOrder()->limit(50)->get();
        return view('student.Test.test-page', $data);
    }



    public function submit(Request $request)
    {
        // Retrieve submitted answers
        $submittedAnswers = $request->input('answers', []);
        if (empty($submittedAnswers)) {
            dd('No answers were submitted. Please check the form data.');
        }
    
        // Fetch all questions and their options
        $questions = Question::whereIn('id', array_keys($submittedAnswers))->get();

        $total_question_no = $questions->count();
        $correctAnswersCount = 0;
        $results = [];
    
        // Loop through each question to check if the answer is correct
        foreach ($questions as $question) {
            $correctAnswer = $question->answer; // e.g., 'A', 'B', etc.
            $submittedAnswer = $submittedAnswers[$question->id]; // Prevent undefined index error
            $isCorrect = $submittedAnswer === $correctAnswer;
    
            if ($isCorrect) {
                $correctAnswersCount++;
            }

            // Populate results data structure
            $results[] = [
                'question' => $question->question,
                'options' => $question->options, // Accessing JSON directly
                'user_answer' => $submittedAnswer,
                'answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'reason' => $question->reason ?? 'No reason provided',
            ];
        }
    
        // Calculate score as percentage
        $score = ($correctAnswersCount / $questions->count()) * 100;
    
        // Return the view with the score and results
        return view('student.Test.test-result-page', compact('score', 'results'));
    }
    



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
