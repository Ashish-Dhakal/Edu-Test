<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('result_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('test_results')->onDelete('cascade'); // Link to test_results table
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Optional: to simplify user-specific queries
            $table->text('question');
            $table->json('options'); // Store options in JSON format (e.g., {"A": "...", "B": "...", ...})
            $table->string('correct_answer');
            $table->boolean('is_correct');
            $table->text('reason')->nullable();
            $table->string('submitted_answer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_responses');
    }
};
