<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Http\Controllers\LoanRepaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class LoanController extends Controller
{
    use ApiHelpers;

    public function createLoan(Request $request): JsonResponse
    {

        $user = $request->user();
        if ( $this->isCustomer($user) ) {
            $validator = Validator::make($request->all(), $this->loanValidationRules());
            if ($validator->passes()) {
                if ( $request->input('loan_amount') > 0 ) {
                    if ( $request->input('loan_term') > 0 ) {
                        $loan = new Loan();
                        $loan->customer_id = $user->id;
                        $loan->loan_amount = $request->input('loan_amount');
                        $loan->loan_term = $request->input('loan_term');
                        $loan->loan_date = date("Y-m-d H:i:s");
                        $loan->save();

                        return $this->onSuccess($loan, 'Loan Created');

                    } else {
                        return $this->onError(400, 'Invalid Loan Term');
                    }
                } else {
                    return $this->onError(400, 'Invalid Loan Amount');
                }   
            }
        
            return $this->onError(400, $validator->errors());
        }

        return $this->onError(401, 'Unauthorized Access');
    }

    public function loans(Request $request): JsonResponse
    {
    	$user = $request->user();
        if ( $this->isAdmin($user) ) {
	        $loans = DB::table('loans')->get();
	        return $this->onSuccess($loans, 'Loans Retrieved');
	    } else if ( $this->isCustomer($user) ) {
	        $loans = DB::table('loans')->where('customer_id', $user->id)->get();
	        return $this->onSuccess($loans, 'Loans Retrieved');
	    }
	    return $this->onError(401, 'Unauthorized Access');
    }

    public function singleLoan(Request $request, $id): JsonResponse
    {
        $loan = DB::table('loans')->where('id', $id)->first();
        return $this->onSuccess($loan, 'Loans Retrieved');
    }

    public function updateLoan(Request $request, $id): JsonResponse
    {
    	$user = $request->user();
        if ( $this->isAdmin($user) ) {

    		$get_loan = DB::table('loans')->where('id', $id)->where('status', 'PENDING')->first();
	    	
	    	if ( is_null($get_loan) ) {
	    		return $this->onError(400, 'Invalid Loan Id');
	    	}

	        $validator = Validator::make($request->all(), $this->loanStatusUpdatesValidationRules());
	        if ($validator->passes()) {
	            
	            $this->loadStatusUpdate($id, strtoupper($request->input('status')));

	            if ( strtoupper($request->input('status')) == 'APPROVED' ) {
	            	(new LoanRepaymentController)->createLoanRepayment($get_loan);
	            }

	            return $this->onSuccess($get_loan, 'Loan Updated');
	        }
	        return $this->onError(400, $validator->errors());
	    }
	    return $this->onError(401, 'Unauthorized Access');
    }

    public function loadStatusUpdate($id, $status)
    {
    	$loan = Loan::find($id);
        $loan->status = $status;
        if ($status == 'PAID') {
        	$loan->loan_repaid_date = date("Y-m-d H:i:s");
        } else {
        	$loan->approved_reject_date = date("Y-m-d H:i:s");
        }        
        $loan->save();
    }
}
