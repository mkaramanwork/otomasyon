<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Category;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index() {
        $questions = Question::with('category')->get();
        return view('questions.index', compact('questions'));
    }

    public function create() {
        $categories = Category::all();
        return view('questions.create', compact('categories'));
    }

    public function store(Request $request) {
        Question::create($request->only(
            'question_text','option_a','option_b','option_c','option_d',
            'correct_option','difficulty','category_id'
        ));
        return redirect()->route('questions.index')->with('success','Soru eklendi.');
    }

    public function edit(Question $question) {
        $categories = Category::all();
        return view('questions.edit', compact('question','categories'));
    }

    public function update(Request $request, Question $question) {
        $question->update($request->only(
            'question_text','option_a','option_b','option_c','option_d',
            'correct_option','difficulty','category_id'
        ));
        return redirect()->route('questions.index')->with('success','Soru güncellendi.');
    }

    public function destroy(Question $question) {
        $question->delete();
        return redirect()->route('questions.index')->with('success','Soru silindi.');
    }
}