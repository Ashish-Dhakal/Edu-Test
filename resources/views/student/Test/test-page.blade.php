@extends('layouts.app')

@section('content_header')
    <!-- Timer and Attempt Counter Bar -->
    <div class="row mb-3 w-100">
        <div class="col-md-12">
            <div class="timer-bar d-flex justify-content-between align-items-center p-3 bg-light border rounded">
                <div>
                    <button id="submit-test-btn" class="btn btn-success">Submit Test</button>
                </div>
                <div>
                    <div id="countdown">Time Remaining: 30:00</div>
                </div>
                <div>
                    <div id="clickedQuestionsCount">Questions Answered: 0</div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content_body')

    <div class="container">
        <div class="row">
            {{-- Left side: list of questions and options --}}
            <div class="col-md-8">
                <div class="card-body">
                    {{-- Display questions --}}
                    @foreach ($questions as $index => $question)
                        <div class="mb-4 question-container d-none" id="question-{{ $index + 1 }}">
                            <strong class="question-title">{{ $loop->iteration }}: {{ $question->question }}</strong>
                            <ul class="list-unstyled">
                                @foreach (json_decode($question->options, true) as $optionIndex => $option)
                                    <li class="option-item">
                                        <label class="custom-radio-label">
                                            <input type="radio" name="question_{{ $index }}"
                                                value="{{ $option }}" class="answer-option"
                                                id="question_{{ $index }}_option_{{ $optionIndex }}"
                                                data-question-number="{{ $index + 1 }}">
                                            <span class="option-text">{{ $option }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                    {{-- Navigation buttons --}}
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-secondary" id="prev-btn" disabled>Previous</button>
                        <button class="btn btn-primary" id="next-btn">Next</button>
                    </div>
                </div>
            </div>

            {{-- Right side: list of question numbers --}}
            <div class="col-md-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap">
                        @foreach ($questions as $index => $question)
                            <a href="#question-{{ $index + 1 }}" id="question-number-{{ $index + 1 }}"
                                class="btn btn-sm question-number-btn m-1">
                                {{ $index + 1 }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('js')
    <script>
        let currentQuestion = 1;
        const totalQuestions = {{ count($questions) }};

        function updateQuestionVisibility() {
            document.querySelectorAll('.question-container').forEach(q => q.classList.add('d-none'));
            document.querySelector('#question-' + currentQuestion).classList.remove('d-none');

            document.getElementById('prev-btn').disabled = currentQuestion === 1;
            document.getElementById('next-btn').disabled = currentQuestion === totalQuestions;
        }

        document.getElementById('next-btn').addEventListener('click', function() {
            if (currentQuestion < totalQuestions) {
                currentQuestion++;
                updateQuestionVisibility();
            }
        });

        document.getElementById('prev-btn').addEventListener('click', function() {
            if (currentQuestion > 1) {
                currentQuestion--;
                updateQuestionVisibility();
            }
        });

        document.querySelectorAll('.question-number-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const questionNumber = this.innerText;
                currentQuestion = parseInt(questionNumber);
                updateQuestionVisibility();
            });
        });

        updateQuestionVisibility();

        document.querySelectorAll('.answer-option').forEach(option => {
            option.addEventListener('change', function() {
                let questionNumber = this.getAttribute('data-question-number');
                let questionNumberBtn = document.querySelector('#question-number-' + questionNumber);
                if (questionNumberBtn) {
                    questionNumberBtn.classList.add('btn-answered');
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let timeRemaining = 30 * 60; // 30 minutes in seconds
            const countdownDisplay = document.getElementById('countdown');

            const timerInterval = setInterval(() => {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;

                countdownDisplay.textContent =
                    `Time Remaining: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    alert('Time is up! Submitting your answers.');
                    // Submit the form automatically or handle time-up logic
                    // document.getElementById('testForm').submit(); // Example form submission
                }

                timeRemaining--;
            }, 1000);
        });
    </script>
@endpush

@section('css')
    <style>
        /* Timer Bar Styling */
        .timer-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: linear-gradient(to right, #f4f6f9, #ffffff);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            padding: 15px 20px;
            border: 1px solid #dee2e6;
            width: 100%;
            transition: all 0.3s ease-in-out;
        }

        .timer-bar:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }

        /* Container Spacing */
        .container {
            /* margin-top: 60px; */
        }

        /* Card Styling */
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
            background: #ffffff;
        }

        .card-header {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            border-bottom: 2px solid #e9ecef;
            padding: 15px 20px;
            font-weight: 600;
            color: #2c3e50;
            border-radius: 12px 12px 0 0;
        }

        .card-body {
            max-height: 700px;
            overflow-y: scroll;
            padding: 20px;
            transition: all 0.2s ease-in-out;
        }

        .card-body:hover {
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        /* Question Container Styling */
        .question-container {
            padding: 20px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #e9ecef;
            margin-bottom: 20px;
            transition: all 0.2s ease;
        }

        .question-container:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transform: translateY(-3px);
            background-color: #f8f9fa;
        }

        .question-title {
            font-size: 20px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        /* Option Styling */
        .option-item {
            margin-bottom: 12px;
        }

        .custom-radio-label {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            row-gap: 5px;
        }

        .custom-radio-label:hover {
            background-color: #e9ecef;
            border-color: #dee2e6;
        }

        /* Custom Radio Button */
        .custom-radio {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #6c757d;
            margin-right: 12px;
            position: relative;
            transition: all 0.3s ease;
        }

        .custom-radio::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #fff;
            transition: transform 0.3s ease;
        }

        input[type="radio"]:checked+.custom-radio {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        input[type="radio"]:checked+.custom-radio::after {
            transform: translate(-50%, -50%) scale(1);
        }

        .option-text {
            font-size: 16px;
            color: #495057;
        }

        /* Question Number Buttons */
        .question-number-btn {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 16px;
            font-weight: 500;
            border: 2px solid #e9ecef;
            background-color: #ffffff;
            color: #495057;
            transition: all 0.2s ease;
            margin: 5px;
        }

        .question-number-btn:hover {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(13, 110, 253, 0.2);
        }

        .btn-answered {
            background-color: #198754;
            color: white;
            border-color: #198754;
        }

        .btn-answered:hover {
            background-color: #157347;
            border-color: #157347;
            color: white;
        }

        /* Scrollbar Styling */
        .card-body::-webkit-scrollbar {
            width: 8px;
        }

        .card-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .card-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .card-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Timer and Counter Styling */
        #timer-display,
        #attempt-counter {
            font-size: 1.1rem;
            font-weight: 500;
            color: #495057;
            padding: 8px 15px;
            border-radius: 6px;
            background-color: #e9ecef;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #timer-display:hover,
        #attempt-counter:hover {
            background-color: #ffffff;
        }

        /* Submit Button Styling */
        #submit-test-btn {
            padding: 10px 25px;
            font-weight: 500;
            border-radius: 8px;
            background: linear-gradient(45deg, #198754, #20c997);
            border: none;
            color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        #submit-test-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            background: linear-gradient(45deg, #157347, #1aa179);
        }

        /* Button Styling for Next/Previous */
        #next-btn,
        #prev-btn {
            background-color: #0d6efd;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            border: none;
            transition: background-color 0.3s ease;
        }

        #next-btn:hover,
        #prev-btn:hover {
            background-color: #0a58ca;
        }

        #next-btn:disabled,
        #prev-btn: .btn-answered {
            background-color: #198754;
            /* Green */
            color: white;
            border-color: #198754;
        }

        .btn-answered:hover {
            background-color: #157347;
            border-color: #157347;
            color: white;
        }
    </style>
@stop
