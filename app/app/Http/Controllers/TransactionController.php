<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\TransactionTraits;

class TransactionController extends Controller
{
    use TransactionTraits;
    private $transactionResponse;



    public function transactions()
    {
        $transactions = $this->index();
        return response(['transactions' => $transactions]);
    }

    public function performTransaction(Request $request, $id = null)
    {

        switch ($request->transaction_type) {
            case 'deposit':
                $this->transactionResponse = $this->deposit($request);
                break;
            case 'withraw':
                $this->transactionResponse = $this->withraw($request);
                break;
            case 'transfer':
                $this->transactionResponse = $this->transfer($request, $id);
                break;
            default:
                # code...
                break;
        }
        return response(['message' => $this->transactionResponse]);
    }
}
