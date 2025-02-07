<?php

namespace App\Http\Controllers\Api;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Auth;

class BookReturnController extends Controller
{
    public function listOfBooks()
    {
        $books = Auth::user()->loans()
            ->where('status', 'borrowed')
            ->with('books')
            ->get()
            ->flatMap->books
            ->unique('id');

        return api_response('Successfully retrieved the list of borrowed books.', BookResource::collection($books));
    }

    public function returnBooks()
    {
        $loanIds = request('loan-ids');

        DB::beginTransaction();

        try {
            $loans = $this->getLoans($loanIds);

            if ($loans->isEmpty()) {
                DB::rollBack();
                return api_response('No borrowed books found to return.', null, 404);
            }

            $this->updateLoanStatus($loans);
            $this->createReturnHistory($loans);

            DB::commit();
            return api_response('Successfully returned books.', $loans);
        } catch (\Exception $e) {
            DB::rollBack();
            return api_response('An error occurred while returning books.', null, 500);
        }
    }


    protected function getLoans($loanIds)
    {
        return Auth::user()->loans()
            ->where('status', 'borrowed')
            ->whereIn('id', $loanIds)
            ->get();
    }

    protected function updateLoanStatus($loans)
    {
        return $loans->each->update(['status' => 'returned']);
    }

    protected function createReturnHistory($loans)
    {
        return $loans->each(function ($loan) {
            $loan->returnBooks()->create([
                'return_date' => now()
            ]);
        });
    }
}
