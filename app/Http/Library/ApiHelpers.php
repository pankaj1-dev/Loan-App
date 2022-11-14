<?php
namespace App\Http\Library;

use Illuminate\Http\JsonResponse;

trait ApiHelpers
{
    protected function isAdmin($user): bool
    {
        if (!empty($user)) {
            if ( $user->role == 'Admin' ) {
                return true;
            }
        }
        return false;
    }

    protected function isCustomer($user): bool
    {
        if (!empty($user)) {
            if ( $user->role == 'Customer' ) {
                return true;
            }
        }
        return false;
    }

    protected function onSuccess($data, string $message = '', int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function onError(int $code, string $message = ''): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
        ], $code);
    }

    protected function registerValidationRules(): array
    {
        return [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];
    }

    protected function loginValidationRules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ];
    }

    protected function loanValidationRules(): array
    {
        return [
            'loan_amount' => 'required|numeric',
            'loan_term' => 'required|numeric'
        ];
    }

    protected function loanStatusUpdatesValidationRules(): array
    {
        return [
            'status' => 'required|in:APPROVED,REJECT,Approved,Reject'
        ];
    }

    protected function loanRepaymentValidationRules(): array
    {
        return [
            'repayment_amount' => 'required|numeric'
        ];
    }
}