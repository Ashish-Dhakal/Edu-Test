<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\Subject\TestController;
use App\Http\Controllers\Student\Subject\SubjectController;
use App\Http\Controllers\Student\Course\ExploreCourseController;
// use App\Http\Controllers\Student\ExploreCourse;


Auth::routes();


Route::middleware('auth')->group(function () {

    // course Route
Route::get('/explore-course' , [ExploreCourseController::class ,'index'])->name('explore-course.index');


// subject route
Route::get('/subject' , [SubjectController::class , 'index'])->name('subject.index'); // adding subject-name slug later
Route::get('/subject/subject-detail' , [SubjectController::class , 'detail'])->name('subject.detail'); // adding subject-name slug later



// route for subject test
Route::get('/test' , [TestController::class , 'index'])->name('test.index');
Route::post('/test/submit', [TestController::class, 'submit'])->name('test.submit');


Route::get('/test/subject' , [TestController::class , 'subjectTest'])->name('subjecttest');// adding subject-name slug later




});