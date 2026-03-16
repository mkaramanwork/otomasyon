<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Exam;
use App\Models\Category;
use App\Models\Question;
use App\Models\ExamAssignment;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;



class ExamController extends Controller
{
    // Sınav listesi
    public function index() {
        $exams = Exam::with('category','assignments')->get();

        // Kritik sınavlar: henüz tamamlanmamış olanlar
        $criticalExams = $exams->filter(function($exam){
            return $exam->assignments->where('completed',0)->count() > 0;
        });

        return view('exams.index', compact('exams','criticalExams'));
    }

    // Sınav oluşturma formu
    public function create() {
        $categories = Category::all();
        $questions = Question::with('category')->get();
        return view('exams.create', compact('categories','questions'));
    }


    // Sınav kaydetme
    public function store(Request $request) {
        $exam = Exam::create($request->only('title','category_id','question_count','created_by'));

        // seçilen soruları pivot tabloya kaydet
        if($request->has('question_ids')) {
            foreach($request->question_ids as $qid) {
                $exam->questions()->attach($qid);
            }
        }

        return redirect()->route('exams.index')->with('success','Sınav oluşturuldu.');
    }


    // Kullanıcıya sınav atama
    public function assign(Request $request, Exam $exam) {
        foreach($request->user_ids as $userId) {
            ExamAssignment::create([
                'exam_id'=>$exam->id,
                'user_id'=>$userId,
                'assigned_at'=>now(),
                'completed'=>0,
            ]);
        }
        return back()->with('success','Sınav kullanıcıya gönderildi.');
    }

public function solve(Exam $exam) {
    $exam->load('questions');
    return view('exams.solve', compact('exam'));
}

public function submit(Request $request, Exam $exam)
{
    $answers = $request->input('answers', []);

    $assignment = $exam->assignments()
        ->where('user_id', auth()->id())
        ->with('answers')
        ->firstOrFail();

    foreach($exam->questions as $q) {
        $given = $answers[$q->id] ?? null;
        $isCorrect = $given === $q->correct_option;

        Answer::create([
            'assignment_id'   => $assignment->id,
            'question_id'     => $q->id,
            'selected_option' => $given,
            'is_correct'      => $isCorrect,
        ]);
    }

    $assignment->update(['completed' => 1]);

    // ✅ Doğru / yanlış hesapla
    $total   = $exam->questions->count();
    $correct = Answer::where('assignment_id',$assignment->id)->where('is_correct',true)->count();
    $wrong   = $total - $correct;

    // ✅ Mail gönderimi
    $recipients = ['mrt.krmn.55@gmail.com'];
    $userName   = auth()->user()->name;

    $body = "Sınav Tamamlandı!\n\n".
            "Kullanıcı: {$userName}\n".
            "Sınav: {$exam->title}\n".
            "Toplam Soru: {$total}\n".
            "Doğru: {$correct}\n".
            "Yanlış: {$wrong}\n";

    Mail::raw($body, function($message) use ($recipients) {
        $message->to($recipients)
                ->subject('Sınav Tamamlandı - Sonuç Bilgisi');
    });

    return redirect()->route('exams.results',$exam->id)
                     ->with('success','Sınav başarıyla tamamlandı ve sonuç mail ile iletildi.');
}


    // Sonuç raporu
    public function results(Exam $exam)
    {
        // Kullanıcının assignment kaydını bul
        $assignment = $exam->assignments()
            ->where('user_id', auth()->id())
            ->with('answers.question')
            ->firstOrFail();

        return view('exams.results', compact('exam','assignment'));
}


    public function edit(Exam $exam) {
        $categories = Category::all();
        $questions = Question::with('category')->get(); // ✅ soruları da çekiyoruz
        return view('exams.edit', compact('exam','categories','questions'));
    }
    public function show(Exam $exam) {
        $exam->load('category','questions','assignments.user');
        $users = User::all(); // ✅ artık doğru modelden geliyor
        return view('exams.show', compact('exam','users'));
    }




    public function update(Request $request, Exam $exam) {
        // Sınav bilgilerini güncelle
        $exam->update($request->only('title','category_id','question_count'));

        // Pivot ilişkisini güncelle
        if($request->has('question_ids')) {
            // önce mevcut ilişkileri temizle
            $exam->questions()->sync($request->question_ids);
        } else {
            // hiç soru seçilmediyse pivotu boşalt
            $exam->questions()->detach();
        }

        return redirect()->route('exams.index')->with('success','Sınav güncellendi.');
    }


public function assignForm(Exam $exam) {
    $users = User::all(); // sistemdeki kullanıcılar
    return view('exams.assign', compact('exam','users'));
}
public function destroy(Exam $exam) {
    $exam->delete();
    return redirect()->route('exams.index')->with('success','Sınav silindi.');
}


}