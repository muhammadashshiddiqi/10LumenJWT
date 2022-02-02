<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(){
        return response()->json(Auth::user()->notes);
    }

    public function show(HttpRequest $request, $id){
        $note = Note::where('user_id', $id)->where('user_id', Auth::user()->id)->first();

        if(empty($note)){
            abort(404, 'Data not found.');
        }
        return response()->json($note);
    }

    public function save(HttpRequest $request){
        $field = $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'count' => 'required|min:1',
            'price' => 'required|min:1',
            'type' => 'required|in:income,expense',
            'date' => 'required|date'
        ]);

        $note = new Note();
        $note->title = $field['title'];
        $note->description = $field['description'];
        $note->count = $field['count'];
        $note->price = $field['price'];
        $note->type = $field['type'];
        $note->date = date('Y-m-d', strtotime($field['date']));
        $note->user_id = Auth::user()->id;

        $note->save();

        return response()->json($note);
    }

    public function update(HttpRequest $request, $id){

        $note = Note::where('user_id', $id)->where('user_id', Auth::user()->id)->first();

        if (empty($note)) {
            abort(404, 'Data not found.');
        }

        $field = $this->validate($request, [
            'title' => 'required|max:255',
            'description' => 'required',
            'count' => 'required|min:1',
            'price' => 'required|min:1',
            'type' => 'required|in:income,expense',
            'date' => 'required|date'
        ]);

        $note->title = $field['title'];
        $note->description = $field['description'];
        $note->count = $field['count'];
        $note->price = $field['price'];
        $note->type = $field['type'];
        $note->date = date('Y-m-d', strtotime($field['date']));

        $note->save();
        return response()->json($note);

    }

    public function delete(HttpRequest $request, $id){
        $note = Note::where('user_id', $id)->where('user_id', Auth::user()->id)->first();

        if (empty($note)) {
            abort(404, 'Data not found.');
        }

        $note->delete();
        return response()->json(['message' => 'Data deleted']);
    }
}
