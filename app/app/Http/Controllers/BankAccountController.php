<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    //


    public function store(Request $request)
    {

        $bankAccount = new BankAccount();
        DB::beginTransaction();


        try {
            // adding bank account
            $bankAccount->user_id = Auth::id();
            $bankAccount->type = $request->type;
            $bankAccount->amount = intVal($request->initial_deposit);

            if ($bankAccount->save()) {
                DB::commit();

                return response(['message' => 'Bank Account created successfully']);
            } else {
                return response(['message' => $bankAccount]);
            }
        } catch (\Exception $e) {
            DB::rollback();

            return response(['message' => 'Creation of account failed']);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();


        try {
            if (Auth::user()) {
                $bankAccount = BankAccount::findOrfail($id);
                $bankAccount->user_id = Auth::id();
                $bankAccount->type = $request->type;
                $bankAccount->amount = $request->initial_deposit;

                $bankAccount->save();
                DB::commit();

                return response(['message' => 'Update Bank Account Success']);
            }
        } catch (\Exception $e) {
            DB::rollback();

            return response(['message' => 'Update Failed']);
        }
    }
}
