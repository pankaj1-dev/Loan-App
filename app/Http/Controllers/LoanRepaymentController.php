<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Http\Controllers\LoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class LoanRepaymentController extends Controller
{
     use ApiHelpers; 

    public function createLoanRepayment($loan)
    {
    	$schedule_repayment_amount = ($loan->loan_amount) / ($loan->loan_term);

    	$loan_date = Carbon::create($loan->loan_date);

		for ($i=0; $i < $loan->loan_term; $i++) { 
			
			$schedule_repayment_date = $loan_date->addDays(7);

    		$loan_repayment = new LoanRepayment();
	        $loan_repayment->loan_id = $loan->id;
	        $loan_repayment->schedule_repayment_amount = $schedule_repayment_amount;
	        $loan_repayment->schedule_repayment_date = $schedule_repayment_date;
	        $loan_repayment->save();
    	}
    }

    public function loanRepayment(Request $request, $id): JsonResponse
    {
		$get_loan_repayment = DB::table('loan_repayments')->where('id', $id)->where('status', 'PENDING')->get();

		if ( $get_loan_repayment->count() != 1  ) {
			return $this->onError(400, 'Invalid Repayment Id');
		} else {

			$loan_id = $get_loan_repayment[0]->loan_id;
			$repayment_amount = $request->input('repayment_amount');

			$get_loan = DB::table('loans')->where('id', $loan_id)->get();

			if ( $get_loan->count() != 1  ) {		
				return $this->onError(400, 'Invalid Loan Id');	
			} else {
				if ( $get_loan[0]->status == 'PAID' ) {
					return $this->onError(400, 'Loan Already Paid');	
				} else {
					$loan_amount = $get_loan[0]->loan_amount;
					$repaid_loan_amount = $get_loan[0]->repaid_loan_amount;
					$loan_term = $get_loan[0]->loan_term;

					$validator = Validator::make($request->all(), $this->loanRepaymentValidationRules());
			        if ($validator->passes()) {

			        	$remain_loan_amount = $loan_amount - $repaid_loan_amount;

						$pending_repayments = DB::table('loan_repayments')->where('loan_id', $loan_id)->where('status', 'PENDING')->get()->count();

			        	$schedule_repayment_amount = ($remain_loan_amount) / ($pending_repayments);	

						if ( ($repayment_amount >= $schedule_repayment_amount) && ($loan_amount >= ($repaid_loan_amount+$repayment_amount)) ) {
			        		
			        		// Update Loan Repayment
			        		$this->updateLoanRepayment($id, $repayment_amount);

					        $repaid_loan_amount += $repayment_amount;

					        // Update Loan
					        $loan = Loan::find($loan_id);
					        $loan->repaid_loan_amount = $repaid_loan_amount;
					        $loan->save();

					    } else {
			        		return $this->onError(400, 'Total repayment amount is pending as '.$remain_loan_amount.' with '.$pending_repayments.' loan term. <br> Repayment amount should greater or equal to the scheduled repayment amount i.e. '.$schedule_repayment_amount);
			        	}			
				    	
				    	$loan_repayments = DB::table('loan_repayments')->where('loan_id', $loan_id)->where('status', '=', 'PAID')->get();

				    	if ( $loan_repayments->count() == $loan_term || $loan_amount == $repaid_loan_amount ) {

					        (new LoanController)->loadStatusUpdate($loan_id, 'PAID');
						}   

						if ( $loan_amount == $repaid_loan_amount ) {
							$pending_repayments_update = DB::table('loan_repayments')->where('loan_id', $loan_id)->where('status', 'PENDING')->get();

						 	foreach ($pending_repayments_update as $key => $value) {
						 		$this->updateLoanRepayment($value->id, 0);
						 	}
						 } 

				        return $this->onSuccess($loan_repayments, 'Loan Updated');
				    }

				    return $this->onError(400, $validator->errors());
				}
			}
		}
    }

    public function updateLoanRepayment($id, $repayment_amount)
    {
    	$loan_repayment = LoanRepayment::find($id);
        $loan_repayment->repayment_amount = $repayment_amount;
        $loan_repayment->repayment_date = date("Y-m-d H:i:s");
        $loan_repayment->status = 'PAID';
        $loan_repayment->save();
    }
}
