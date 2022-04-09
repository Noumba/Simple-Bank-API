<?php

namespace App\Http\Traits;

use App\Models\BankAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait TransactionTraits
{

    public function index()
    {
        // $transactions = DB::table('transactions')
        //     ->where('bank_account_id', Auth()->user()->bankAccounts->)
        //     ->select('bank_account_id', 'transaction_type', 'amount')
        //     ->get();

        $accIds = auth()->user()->bankAccounts;
        $account_ids = [];
        foreach ($accIds as $accId) {
            array_push($account_ids, $accId['id']);
        }
        $transactions = Transaction::whereIn('bank_account_id', $account_ids)->get();

        return response(['transactions' => $transactions]);
    }

    public function deposit(Request $request)
    {

        DB::beginTransaction();

        try {
            if (Auth::check()) {
                $transaction = new Transaction();
                $transaction->transaction_type = $request->transaction_type;
                $transaction->amount = intval($request->amount);

                $transaction->bank_account_id = $request->bank_account_id;

                $bankAccount = BankAccount::find(intval($request->bank_account_id));

                $bankAccount->amount = $bankAccount->amount + intval($request->amount);

                $bankAccount->save();
                $transaction->save();

                DB::commit();
                return response(['message' => 'Deposit Successfully', 'account balance' => $bankAccount, 'transaction' => $transaction]);
            }
        } catch (\Exception $e) {
            return response(['message' => 'An error Occured']);
        }
    }

    public function withraw(Request $request)
    {

        DB::beginTransaction();

        try {
            if (Auth::check()) {
                $transaction = new Transaction();
                $transaction->transaction_type = $request->transaction_type;
                $transaction->amount = intval($request->amount);

                $transaction->bank_account_id = $request->bank_account_id;

                $bankAccount = BankAccount::find(intval($request->bank_account_id));
                if (!$bankAccount->amount > intVal($request->amount)) {
                    return response(['message' => 'Unable to withraw. Insufficient funds']);
                }
                $bankAccount->amount = $bankAccount->amount - intVal($request->amount);

                $bankAccount->save();
                $transaction->save();

                DB::commit();
                return response(['message' => 'Withrawal Successfully', 'account balance' => $bankAccount, 'transaction' => $transaction]);
            }
        } catch (\Exception $e) {
            return response(['message' => 'An error Occured']);
        }
    }

    public function transfer(Request $request, $receiving_account_id)
    {
        $this->validate($request, [
            'amount' => 'required|min:0',
        ]);
        DB::beginTransaction();

        try {
            if (Auth::check()) {
                $transaction = new Transaction();
                $transaction->transaction_type = $request->transaction_type;
                $transaction->amount = intVal($request->amount);

                $transaction->bank_account_id = $request->bank_account_id;

                $SendingBankAccount = BankAccount::find(intval($request->bank_account_id));
                $ReceivingBankAccount = BankAccount::find($receiving_account_id);
                if (!$SendingBankAccount->amount > intVal($request->amount)) {
                    return response(['message' => 'Unable to Transfer funds. Insufficient amount']);
                }
                $SendingBankAccount->amount = $SendingBankAccount->amount - intVal($request->amount);
                $ReceivingBankAccount->amount = $ReceivingBankAccount->amount + intVal($request->amount);
                $ReceivingBankAccount->save();
                $SendingBankAccount->save();

                DB::commit();
                return response(['message' => 'Transfer Successfully', 'sending accpont' => $SendingBankAccount, 'receiving accpont' => $ReceivingBankAccount]);
            }
        } catch (\Exception $e) {
            return response(['message' => 'An error Occured']);
        }
    }
}
