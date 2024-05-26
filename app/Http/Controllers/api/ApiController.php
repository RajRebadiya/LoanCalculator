<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\loanaff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
// give me pdf package
use Barryvdh\DomPDF\Facade\PDF;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function aboutUs()
    {
        $about =  '
                <div>
                    <h2>Welcome to Loan Calculator!</h2>
                    <p>At Loan Calculator, our mission is to empower individuals and businesses to make informed financial decisions with ease. We understand that navigating the complexities of loans and financial planning can be challenging. That\'s why we\'ve created a user-friendly, comprehensive loan calculator app designed to simplify the process for you.</p>
                </div>
    
                <div>
                    <h3>Our Vision</h3>
                    <p>We aim to be the go-to platform for all your loan calculation needs, providing accurate, reliable, and easy-to-understand tools to help you plan your finances effectively. Whether you\'re looking to buy a home, finance a car, or manage personal loans, our app is here to guide you every step of the way.</p>
                </div>
    
                <div>
                    <h3>What We Offer</h3>
                    <ul>
                        <li><strong>Accurate Calculations:</strong> Our advanced algorithm ensures that you get precise EMI calculations based on your principal amount, interest rate, and loan tenure.</li>
                        <li><strong>Multiple Loan Types:</strong> Whether it\'s a home loan, car loan, personal loan, or any other type, our calculator can handle it all.</li>
                        <li><strong>Customizable Settings:</strong> Choose between monthly or yearly terms, adjust interest rates, and see how different scenarios impact your repayment plans.</li>
                        <li><strong>PDF Generation:</strong> Easily save and share your calculations by generating a PDF report of your loan details.</li>
                        <li><strong>User-Friendly Interface:</strong> Our app is designed with simplicity in mind, making it easy for users of all ages and backgrounds to navigate and use.</li>
                    </ul>
                </div>
    
                <div>
                    <p>We are committed to maintaining the highest standards of accuracy, privacy, and user satisfaction. Our team continuously works on improving the app by adding new features and enhancing existing ones based on your valuable feedback.</p>
                </div>
            ';
        $response = [
            'status' => 1,
            'code' => 200,
            'message' => 'About Us',
            'data' => array("about" => $about),
        ];
        return $response;
    }

    function get_data($data)
    {
        $token = $data->token;

        $data = loanaff::where('token', $token)->first();
        return response()->json(['code' => 200, 'status' => 1, 'data' => $data, 'message' => 'Data From Loanadd table']);
    }

    public function add_data(Request $request)
    {
        // Define validation rules
        $rules = [
            'token' => 'required',
            'i_rate' => 'required',
            'tenure' => 'required',
            's_date' => 'required',
            'emi' => 'required',
            'total_inter' => 'required',
            'total_amount' => 'required',
            'e_date' => 'required'
        ];

        // Validate the incoming request
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $errorMessage = implode(' ', $errors);
            return response()->json([
                'code' => 400,
                'status' => 0,
                'message' => $errorMessage,
            ]);
        }

        $data = new loanaff();
        $data->token = $request->token;
        $data->loan_amount = $request->loan_amount;
        $data->i_rate = $request->i_rate;
        $data->tenure = $request->tenure;
        $data->s_date = $request->s_date;
        $data->emi = $request->emi;
        $data->total_inter = $request->total_inter;
        $data->total_amount = $request->total_amount;
        $data->e_date = $request->e_date;
        $data->pdf_link = $request->pdf_link;
        $result = $data->save();

        $loan = loanaff::where('token', $request->token)->first();
        echo '<pre>';
        print_r($loan);
        exit();


        $pdf = PDF::loadView('loan', compact('loan'));
        Storage::put('public/pdf/' . $data->pdf_link, $pdf->output());
        echo '<pre>';
        print_r($data->pdf_link);
        exit();
        return response()->json(['code' => 200, 'status' => 1,  'message' => 'Data Added Successfully', 'data' => $data]);
    }


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

        $data = new loanaff();
        $data->token = $request->token;
        $data->loan_amount = $request->loan_amount;
        $data->i_rate = $request->i_rate;
        $data->tenure = $request->tenure;
        $data->s_date = $request->s_date;
        $data->emi = $request->emi;
        $data->total_inter = $request->total_inter;
        $data->total_amount = $request->total_amount;
        $data->e_date = $request->e_date;

        // Generate a unique file name
        $uniqueFileName = 'LoanEmi_' . time() . '.pdf';

        $pdf = PDF::loadView('pdf_template', compact('data'));

        $filePath = public_path('pdfs/' . $uniqueFileName);

        if (!File::exists(public_path('pdfs'))) {
            File::makeDirectory(public_path('pdfs'), 0755, true);
        }

        $pdf->save($filePath);

        // Store only the PDF file name in the database
        $data->pdf_link = $uniqueFileName;

        // Save the loanaff object to the database
        $data->save();

        return response()->json([
            'message' => 'PDF generated and saved successfully.',
            'file_name' => $uniqueFileName,
        ], 200);
    }

    // private function calculateEmiDetails($loan)
    // {
    //     $emiDetails = [];
    //     $amount = $loan['loan_amount'];
    //     $rate = $loan['i_rate'];
    //     $tenure = $loan['tenure'];
    //     $startDate = new \DateTime($loan['s_date']);

    //     $monthlyRate = $rate / (12 * 100);
    //     $emi = ($amount * $monthlyRate * pow(1 + $monthlyRate, $tenure)) / (pow(1 + $monthlyRate, $tenure) - 1);
    //     $balance = $amount;

    //     for ($i = 1; $i <= $tenure; $i++) {
    //         $interest = $balance * $monthlyRate;
    //         $principalComponent = $emi - $interest;
    //         $balance -= $principalComponent;

    //         $emiDetails[] = [
    //             'number' => $i,
    //             'emi_date' => $startDate->format('M d, Y'),
    //             'emi' => round($emi),
    //             'principal' => round($principalComponent),
    //             'interest' => round($interest),
    //             'balance' => $balance > 0 ? round($balance) : '0',
    //         ];

    //         $startDate->modify('+1 month');
    //     }

    //     return $emiDetails;
    // }
}
