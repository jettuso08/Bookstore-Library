<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use DataTables;
use Excel;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('book.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('book.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation for the submitted inputs
        $validated = $request->validate([
            'name'      => 'required',
            'author'    => 'required',
            'cover'     => 'required'
        ]);

        // Check if book name and author already exists
        $book_exists = Book::where('name', $request->name)->where('author', $request->author)->first();

        if ($book_exists) {
            return redirect()->back()->withErrors(['msg' => 'Book already exists']);
        }

        // Save data to books table
        $book           = new Book;
        $book->name     = $request->name;
        $book->author   = $request->author;
        $book->cover    = $request->cover;
        $book->save();

        return redirect()->route('books.index')->with('success', 'Book has successfully saved!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return redirect()->back()->withErrors(['msg' => 'Book not found']);
        }

        return view('book.edit')->with('book', $book);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validation for the submitted inputs
        $validated = $request->validate([
            'name'      => 'required',
            'author'    => 'required',
            'cover'     => 'required'
        ]);

        // Get book data
        $book = Book::find($id);

        if (!$book) {
            return redirect()->back()->withErrors(['msg' => 'Book not found']);
        }

        // Check if book name and author already exists
        $book_exists = Book::where('name', $request->name)->where('author', $request->author)->whereNot('id', $id)->first();

        if ($book_exists) {
            return redirect()->back()->withErrors(['msg' => 'Book already exists']);
        }

        // Save data to books table
        $book->name     = $request->name;
        $book->author   = $request->author;
        $book->cover    = $request->cover;
        $book->save();

        return redirect()->route('books.index')->with('success', 'Book has successfully saved!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Get book data
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success'   => false,
                'msg'       => 'Book not found!'
            ]);
        }

        $book->delete();

        return response()->json([
            'success'   => true,
            'msg'       => 'Book has successfully deleted!'
        ]);
    }

    // Get all books
    public function table(Request $request)
    {
        $books = Book::latest()->get();

        return Datatables::of($books)
            ->addIndexColumn()
            ->addColumn('action', function($book){
                $btn = '
                    <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $book->id . '" data-original-title="View" class="btn btn-success btn-sm view-book">View</a>
                    <a href="' . route('books.edit', [$book->id]) . '" data-toggle="tooltip"  data-id="' . $book->id . '" data-original-title="Edit" class="btn btn-primary btn-sm">Edit</a>
                    <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $book->id . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-book">Delete</a>
                ';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Get book detail
    public function detail($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success'   => false,
                'msg'       => 'Book not found!'
            ]);
        }

        return response()->json([
            'success'   => true,
            'book'      => $book->toArray(),
            'msg'       => 'Book found!'
        ]);
    }

    // Import books
    public function import(Request $request)
    {
        if ($request->hasFile('file')) {
            // Read csv file and put it into array
            $path           = $request->file('file')->getRealPath();
            $imported_books = Excel::toArray([], $path);
            $imported_books = count($imported_books) > 0 ? $imported_books[0] : array();

            if (count($imported_books)) {
                // Remove header
                array_shift($imported_books);
                
                $books_new      = array();

                foreach ($imported_books as $key => $imported_book) {
                    $book_name      = isset($imported_book[0]) ? $imported_book[0] : '';
                    $book_author    = isset($imported_book[1]) ? $imported_book[1] : '';
                    $book_cover     = isset($imported_book[2]) ? $imported_book[2] : '';
                    $id             = '';

                    // Check if book name and author is already exists
                    $book_exists = Book::where('name', $book_name)->where('author', $book_author)->first();

                    if ($book_exists) {
                        $books_update[] = array(
                            'id'        => $id,
                            'name'      => $book_name,
                            'author'    => $book_author,
                            'cover'     => $book_cover
                        );

                        // Update existing books
                        $book_exists->name      = $book_name;
                        $book_exists->author    = $book_author;
                        $book_exists->cover     = $book_cover;
                        $book_exists->save();
                    }
                    else {
                        $books_new[] = array(
                            'name'      => $book_name,
                            'author'    => $book_author,
                            'cover'     => $book_cover
                        );
                    }
                }
                
                // Save new books
                if (count($books_new) > 0) {
                    Book::insert($books_new);
                }
                
                return response()->json([
                    'success'   => true,
                    'msg'       => 'Book has successfully imported!'
                ]);
            }
            else {
                return response()->json([
                    'success'   => false,
                    'msg'       => 'No data found!'
                ]);
            }
        }
        else {
            return response()->json([
                'success'   => false,
                'msg'       => 'Please upload a file'
            ]);
        }
    }
}
