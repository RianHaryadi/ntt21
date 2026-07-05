<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Question;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'questionable_type' => 'required|in:destination,hotel',
            'questionable_id'   => 'required|integer',
            'body'              => 'required|string|max:1000',
        ]);

        $model = match ($request->questionable_type) {
            'destination' => Destination::findOrFail($request->questionable_id),
            'hotel'       => Hotel::findOrFail($request->questionable_id),
        };

        $model->questions()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        return back()->with('success', 'Pertanyaan Anda berhasil dikirim.');
    }

    public function storeAnswer(Request $request, Question $question)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $question->answers()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        return back()->with('success', 'Jawaban Anda berhasil dikirim.');
    }

    public function destroy(Question $question)
    {
        abort_unless($question->user_id === auth()->id(), 403);

        $question->delete();

        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function destroyAnswer(Answer $answer)
    {
        abort_unless($answer->user_id === auth()->id(), 403);

        $answer->delete();

        return back()->with('success', 'Jawaban berhasil dihapus.');
    }
}
