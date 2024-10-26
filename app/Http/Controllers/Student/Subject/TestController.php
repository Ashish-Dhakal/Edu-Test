<?php

namespace App\Http\Controllers\Student\Subject;

use App\Models\Question;
use App\Models\TestResult;
use Illuminate\Http\Request;
use App\Models\ResultResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDO;

use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $username = Auth::user()->name;
        $user_id = Auth::user()->id;
        $test_result = new TestResult();
        $test_result->user_id = $user_id;
        $test_result->username = $username;
        $test_result->save();

        $testResultId = $test_result->id; // This will give you the ID of the saved record


        // dd($testResultId);

        return view('student.Test.test-info', ['testResultId' => $testResultId]);
    }

    public function subjectTest($id)
    {
        $data['test_id'] = $id;
        $result = TestResult::find($id); // Replace with your actual model and ID

        if (

            is_null($result->total_time) && is_null($result->correct_answers)
            && is_null($result->wrong_answers) && is_null($result->avg_time_per_question)
        ) {
            // dd("test ko if");
            $data['questions'] = Question::inRandomOrder()->limit(50)->get();
            return view('student.Test.test-page', $data);
        } else {
            return redirect()->route('home');
        }
    }

    // public function submit(Request $request)
    // {
    //     // Retrieve submitted answers
    //     $submittedAnswers = $request->input('answers', []);
    //     if (empty($submittedAnswers)) {
    //         dd('No answers were submitted. Please check the form data.');
    //     }

    //     $questions = Question::whereIn('id', array_keys($submittedAnswers))->get();
    //     $totalQuestionNo = $questions->count();
    //     $correctAnswersCount = 0;
    //     $results = [];

    //     foreach ($questions as $question) {
    //         $correctAnswer = $question->answer;
    //         $submittedAnswer = $submittedAnswers[$question->id];
    //         $isCorrect = $submittedAnswer === $correctAnswer;

    //         if ($isCorrect) {
    //             $correctAnswersCount++;
    //         }

    //         $results[] = [
    //             'question' => $question->question,
    //             'options' => $question->options,
    //             'user_answer' => $submittedAnswer,
    //             'answer' => $correctAnswer,
    //             'is_correct' => $isCorrect,
    //             'reason' => $question->reason ?? 'No reason provided',
    //         ];
    //     }

    //     $score = ($correctAnswersCount / $totalQuestionNo) * 100;
    //     $wrongAnswersCount = $totalQuestionNo - $correctAnswersCount;

    //     $remainingTime = $request->input('total_time', '30:00');
    //     [$minutes, $seconds] = explode(':', $remainingTime);
    //     $remainingTimeInSeconds = ($minutes * 60) + $seconds;
    //     $totalTimeUsed = 1800 - $remainingTimeInSeconds;
    //     $avgTimePerQuestion = $totalQuestionNo ? ($totalTimeUsed / $totalQuestionNo) : 0;

    //     $testResult = new TestResult();
    //     $testResult->user_id = Auth::id();
    //     $testResult->username = Auth::user()->name;
    //     $testResult->total_time = $totalTimeUsed;
    //     $testResult->correct_answers = $correctAnswersCount;
    //     $testResult->wrong_answers = $wrongAnswersCount;
    //     $testResult->avg_time_per_question = round($avgTimePerQuestion, 2);
    //     $testResult->save();


    //     $testId = $testResult->id;

    //     foreach ($questions as $question) {
    //         $isCorrect = $submittedAnswers[$question->id] === $question->answer;

    //         ResultResponse::create([
    //             'test_id' => $testId,                  // Link to test_results entry
    //             'user_id' => Auth::id(),
    //             'question' => $question->question,
    //             'options' => json_encode($question->options),  // Convert options to JSON format
    //             'correct_answer' => $question->answer,
    //             'is_correct' => $isCorrect,
    //             'reason' => $question->reason,
    //             'submitted_answer' => $submittedAnswers[$question->id],
    //         ]);
    //     }

    //     return view('student.Test.test-result-page', compact('score', 'results'));
    // }



    public function submit(Request $request, $id)
    {
        // dd($id);
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Retrieve submitted answers
            $submittedAnswers = $request->input('answers', []);
            if (empty($submittedAnswers)) {
                throw new \Exception('No answers were submitted. Please check the form data.');
            }

            $questions = Question::whereIn('id', array_keys($submittedAnswers))->get();
            $totalQuestionNo = $questions->count();
            $correctAnswersCount = 0;
            $results = [];

            foreach ($questions as $question) {
                $correctAnswer = $question->answer;
                $submittedAnswer = $submittedAnswers[$question->id];
                $isCorrect = $submittedAnswer === $correctAnswer;

                if ($isCorrect) {
                    $correctAnswersCount++;
                }

                $results[] = [
                    'question' => $question->question,
                    'options' => $question->options,
                    'user_answer' => $submittedAnswer,
                    'answer' => $correctAnswer,
                    'is_correct' => $isCorrect,
                    'reason' => $question->reason ?? 'No reason provided',
                ];
            }

            $score = ($correctAnswersCount / $totalQuestionNo) * 100;
            $wrongAnswersCount = $totalQuestionNo - $correctAnswersCount;

            $remainingTime = $request->input('total_time', '30:00');
            [$minutes, $seconds] = explode(':', $remainingTime);
            $remainingTimeInSeconds = ($minutes * 60) + $seconds;
            $totalTimeUsed = 1800 - $remainingTimeInSeconds;
            $avgTimePerQuestion = $totalQuestionNo ? ($totalTimeUsed / $totalQuestionNo) : 0;


            $testResult = TestResult::find($id); // Replace with your actual model and ID
            if (
                is_null($testResult->total_time) &&
                is_null($testResult->correct_answers) &&
                is_null($testResult->wrong_answers) &&
                is_null($testResult->avg_time_per_question)
            ) {

                $testResult->total_time = $totalTimeUsed;
                $testResult->correct_answers = $correctAnswersCount;
                $testResult->wrong_answers = $wrongAnswersCount;
                $testResult->avg_time_per_question = round($avgTimePerQuestion, 2);
                $testResult->save();
            } else {
                return redirect()->route('home');
            }
            // Save to test_results table


            $testId = $testResult->id;

            // Save each question's response to result_responses table
            foreach ($questions as $question) {
                $isCorrect = $submittedAnswers[$question->id] === $question->answer;

                ResultResponse::create([
                    'test_id' => $testId,
                    'user_id' => Auth::id(),
                    'question' => $question->question,
                    'options' => json_encode($question->options),  // Convert options to JSON format
                    'correct_answer' => $question->answer,
                    'is_correct' => $isCorrect,
                    'reason' => $question->reason,
                    'submitted_answer' => $submittedAnswers[$question->id],
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Return the view with the score and results
            return view('student.Test.test-result-page', compact('score', 'results'));
        } catch (\Exception $e) {
            // Roll back the transaction if any error occurs
            DB::rollBack();
            return back()->withErrors(['error' => 'There was an error saving your test. Please try again.']);
        }
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
