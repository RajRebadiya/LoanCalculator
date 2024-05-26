<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Models\loanaff;

class LoanController extends Controller
{
    //
    public function generateAndSavePDF(Request $request)
    {
        $loan = $request->validate([
            'loan_amount' => 'required|numeric',
            'emi' => 'required|numeric',
            'i_rate' => 'required|numeric',
            'total_inter' => 'required|numeric',
            'tenure' => 'required|numeric',
            'total_amount' => 'required|numeric',
            's_date' => 'required|date',
            'e_date' => 'required|date',
        ]);

        $emiDetails = $this->calculateEmiDetails($loan);

        $pdf = PDF::loadView('loan-pdf', compact('loan', 'emiDetails'));

        $fileName = 'LoanCal.pdf';
        $filePath = public_path('pdfs/' . $fileName);

        if (!File::exists(public_path('pdfs'))) {
            File::makeDirectory(public_path('pdfs'), 0755, true);
        }

        $pdf->save($filePath);

        return response()->json([
            'code' => 200,
            'status' => 1,
            'message' => 'PDF generated and saved successfully.',
            'data' => url('pdfs/' . $fileName),
        ], 200);
    }

    private function calculateEmiDetails($loan)
    {
        $emiDetails = [];
        $amount = $loan['loan_amount'];
        $rate = $loan['i_rate'];
        $tenure = $loan['tenure'];
        $startDate = new \DateTime($loan['s_date']);

        $monthlyRate = $rate / (12 * 100);
        $emi = ($amount * $monthlyRate * pow(1 + $monthlyRate, $tenure)) / (pow(1 + $monthlyRate, $tenure) - 1);
        $balance = $amount;

        for ($i = 1; $i <= $tenure; $i++) {
            $interest = $balance * $monthlyRate;
            $principalComponent = $emi - $interest;
            $balance -= $principalComponent;

            $emiDetails[] = [
                'number' => $i,
                'emi_date' => $startDate->format('M d, Y'),
                'emi' => round($emi),
                'principal' => round($principalComponent),
                'interest' => round($interest),
                'balance' => $balance > 0 ? round($balance) : '0',
            ];

            $startDate->modify('+1 month');
        }

        return $emiDetails;
    }
}
