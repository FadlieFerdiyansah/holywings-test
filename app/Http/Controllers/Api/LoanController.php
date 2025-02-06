<?php

namespace App\Http\Controllers\Api;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function store()
    {
        $user = Auth::user();
        $bookIds = request('books');
    
        $outOfStockBooks = $this->checkStock($bookIds);
    
        if ($outOfStockBooks->isNotEmpty()) {
            $outOfStockBookTitles = $outOfStockBooks->pluck('title')->implode(', ');
            return api_response("The following books are out of stock: {$outOfStockBookTitles}", null, 400);
        }
    
        $loan = $user->loans()->create([
            'loan_date' => now(),
            'due_date' => now()->addMonth(),
            'status' => 'borrowed'
        ]);
    
        $loan->books()->sync($bookIds);
    
        foreach ($bookIds as $bookId) {
            Book::where('id', $bookId)->decrement('qty', 1);
        }
    
        return api_response('Successfully borrowed a book.', LoanResource::collection($user->loans));
    }
    
    protected function checkStock(array $bookIds)
    {
        $books = Book::whereIn('id', $bookIds)->get();
    
        return $books->filter(function ($book) {
            return $book->qty <= 0;
        });
    }
}
