<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BookStoreRequest;

class BookController extends Controller
{
    public function index()
	{
		return api_response('Books retrieved successfully.',Book::where('qty' ,'>', 0)->latest()->get());
	}

	public function store(BookStoreRequest $request)
	{
		$validated = $request->validated();
		$book = Book::create($validated);

		return api_response('Book created successfully.', $book);
	}

	public function update(Book $book, Request $request)
	{
		$validated = $request->validate([
			'category_id' => 'nullable|exists:categories,id',
			'title' => 'nullable|string',
			'author' => 'nullable|string',
			'description' => 'nullable|string',
			'year_published' => 'nullable|integer',
		]);

		$book->update($validated);

		return api_response('Book updated successfully', $book);
	}

	public function destroy(Book $book)
	{
		$book->delete();

		return api_response('Book deleted successfully');
	}
}
