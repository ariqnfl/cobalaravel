<?php

namespace App\Http\Controllers;

use App\Book;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $keyword =$request->get('keyword') ? $request->get('keyword') : '';
        if ($status){
            $books = \App\Book::with('categories')->where('title',"LIKE","%$keyword%")->where('status',strtoupper($status))->paginate(4);
        }else{
            $books = \App\Book::with('categories')->where('title','LIKE',"%$keyword%")->paginate(4);
        }
        return view('books.index',compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['status'] = $request->get('save_action');
        $data['slug'] = $request->title;
        $data['created_by'] = \Auth::user()->id;
        if ($request->file('cover')) {
            $cover_path = $request->file('cover')
                ->store('book-covers', 'public');
            $data['cover'] = $cover_path;
        }
        $books = Book::create($data);
        $books->categories()->attach($request->get('categories'));
        if ($request->get('save_action') == 'PUBLISH') {
            return redirect()
                ->route('books.create')
                ->with('status', 'Book successfully saved and published');
        } else {
            return redirect()
                ->route('books.create')
                ->with('status', 'Book saved as Draft');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $book = \App\Book::findOrFail($id);
        return view('books.edit',compact('book'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $book = \App\Book::findOrFail($id);
        $data = $request->all();
        $data['slug'] = $request->title;
        if ($request->hasFile('cover')){
            $file = $request->file('cover')->store('book-covers','public');
            $data['cover'] = $file;
            \Storage::delete('public/'.$book->cover);
        }
        $book->update($data);
        return redirect()->route('books.edit', ['id'=>$book->id])->with('status','Book Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = \App\Book::findOrFail($id);
        $book->delete();
        return redirect()->route('books.index')->with('status','Book moved to trash');
    }
    public function trash(){
        $books = \App\Book::onlyTrashed()->paginate(4);
        return view('books.trash',compact('books'));
    }
    public function restore($id){
        $book = \App\Book::withTrashed()->findOrFail($id);
        if ($book->trashed()){
            $book->restore();
            return redirect()->route('books.trash')->with('status','Book Successfully restored');
        }else{
            return redirect()->route('books.trash')->with('status','Book is not in trash');
        }
    }
    public function deletePermanent($id){
        $book = \App\Book::withTrashed()->findOrFail($id);
        if (!$book->trashed()){
            return redirect()->route('books.trash')->with('status', 'Book is not in trash!')->with('status_type','alert');
        }else{
            $book->categories()->detach();
            $book->forceDelete();
            return redirect()->route('books.trash')->with('status','Book Permanently Deleted');
        }
    }
}
