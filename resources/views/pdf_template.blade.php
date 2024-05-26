<!DOCTYPE html>
<html>
<head>
    <title>Loan EMI Calculation</title>
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
        }

        #invoice {
            margin-top: 1in;
            margin-right: 0.5in;
            margin-bottom: 1in;
            margin-left: 1in;
        }   
        .no-page-break {
            page-break-inside: avoid;
        }

        .strong {
            font-weight: 100;
        }

        body {
            height: 100vh;
            display: grid;
            place-items: center;
        }

        .invoice {
            width: min(600px, 90vw);
            font: 100 0.7rem 'myriad pro', helvetica, sans-serif;
            border: 0.5px solid black;
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            gap: 3rem;
        }

        .invoice-wrapper {
            display: flex;
            justify-content: space-between;
            padding: 0 1rem;
        }

        .box {
            padding: 10px;
           background: #D2BDFF;
            display: inline-block;
            width: 159px;
            text-align: center;
            height: 45px;
            /* padding-top: 13px; */
            font-size: 16px;
            margin-bottom: 12px;
            font-weight: 600;
            margin-left: 100px;
            margin-top: 13px;
        }

        .invoice-company {
            text-align: right;
        }

        .invoice-company-name {
            font-size: 0.9rem;
            margin-bottom: 1.25rem;
        }

        .invoice-company-address {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .invoice-logo {
            width: 5rem;
            height: 5rem;
        }

        .invoice-billing-company {
            font-size: 0.65rem;
            margin-bottom: 0.25rem;
        }

        .invoice-billing-address {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            gap: 2rem;
            margin: 0.25rem 0;
        }

        .invoice-info:nth-of-type(5) {
            margin-top: 1.5rem;
        }

        .invoice-info-value {
            font-weight: 900;
        }

        .invoicetable {
            width: 100%;
        }

        .invoice-table, th, tr:nth-child(odd) {
            border-collapse: collapse;
            background-color: #684aa7;
            color: white;
            text-align: center;

            /* for table head color change */
        }


        th, td {
            width: calc((600px - 3rem) / 4);
            text-align: center;
            padding: 0.75rem;
        }

        tr:nth-of-type(1) {
            /* background-color: rgba(0, 0, 0, 0.2); */
        }

        tr:nth-of-type(2), tr:nth-of-type(3) {
            /* border-bottom: 0.5px solid rgba(0, 0, 0, 0.25); */
        }

        .invoice-total {
            font-weight: 900;
        }

        .invoice-print {
            font-size: 1.25rem;
            margin: 0 auto;
            cursor: pointer;
            border: 1.25px solid black;
            border-radius: 50%;
            padding: 0.6rem;
        }

        .invoice-print:active {
            background-color: black;
            color: white;
        }
       
        thead , tr:nth-child(even) {
            background: #9475d7;
          
          color: rgb(0, 0, 0);  
          /* for 2 second tr color change */
        }
        thead , tr:nth-child(odd) {
          background: #D2BDFF;

          color: rgb(0, 0, 0);  
          /* for 1 second tr color change */
        }
       
        table {
            width: 80%;
            margin-left: 100px;
            margin-right: 100px;
            border-collapse: collapse;
        }
        table, th, td {
            /* border: 1px solid black; */
        }
        th, td {
            padding: 5px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class='invoice-wrapper'>
        <img src="" alt="logo" >
        <div class='invoice-company'>
            <h2 style="font-size: xx-large; margin-bottom: 20px;" class='invoice-company-name text-center'>Loan EMI Calculator</h2>
            
        </div>
    </div>
    <div class='invoice-wrapper'>
       

        <div class='invoice-details' style='display: flex;'>
            <div class='invoice-info' style="">
                <span class='invoice-info-key box' id='loan_amount'>{{$data['loan_amount']}} <br><span class='strong'>Loan Amount</span></span>
                <span class='invoice-info-key box'>{{$data['emi']}} <br><span class='strong'>EMI</span></span>
            </div>
            <div class='invoice-info'>
                <span class='invoice-info-key box' id='i_rate'>{{$data['i_rate']}} <br><span class="strong">Interest Rate(%)</span></span>
                <span class='invoice-info-key box'>{{$data['total_inter']}} <br><span class="strong">Total Interest</span></span>
            </div>
            <div class='invoice-info'>
                <span class='invoice-info-key box' id='tenure'>{{$data['tenure']}}<br><span class="strong">Tenure(In Month)</span></span>
                <span class='invoice-info-key box'>{{$data['total_amount']}} <br><span class="strong" style="font-size: 14px;">Total Payable Amount</span></span>
            </div>
            <div class='invoice-info'>
                <span class='invoice-info-key box' id='s_date'>{{$data['s_date']}} <br><span class="strong">Start Date</span></span>
                <span class='invoice-info-key box'>{{$data['e_date']}} <br><span class="strong">End Date</span></span>
            </div>
        </div>
       
    </div>

<?php
function calculateEMI($loanAmount, $interestRate, $tenure) {
    if ($tenure <= 0 || $interestRate <= 0) {
        throw new Exception("Tenure and interest rate must be positive values.");
    }

    $monthlyRate = $interestRate / (12 * 100);

    if ($monthlyRate == 0) {
        return ceil($loanAmount / $tenure);
    }

    $denominator = pow(1 + $monthlyRate, $tenure) - 1;

    if ($denominator == 0) {
        throw new Exception('Division by zero error: Denominator is zero.');
    }

    $emi = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $tenure)) / $denominator;
    return ceil($emi);
}

try {
    // Fetch dynamic data from API
    // function fetchLoanDataFromAPI() {
    //     // Example function to fetch data from API
    //     // Replace this with your actual code to fetch data from the API
    //     // For demonstration, returning static data
    //     return [
    //         'loan_amount' => 50000, // Example: Loan Amount in rupees
    //         'interest_rate' => 1.25, // Example: Interest Rate in percentage
    //         'tenure' => 24, // Example: Tenure in months
    //         'start_date' => date("Y-m-d") // Example: Start Date (current date)
    //     ];
    // }

    // // Fetch loan data from API
    // $data = fetchLoanDataFromAPI();

    // Dynamic data from API
    $loanAmount = $data['loan_amount'];
    $interestRate = $data['i_rate'];
    $tenure = $data['tenure'];
    $startDate = $data['s_date'];

    // Calculate EMI
    $emi = calculateEMI($loanAmount, $interestRate, $tenure);

    $remainingLoanAmount = $loanAmount;
    $monthlyRate = $interestRate / (12 * 100);
    $currentDate = strtotime($startDate);
?>

<table>
    <thead id='emi-table-body' class='table-head'>
        <tr>
            <th>EMI #</th>
            <th>Date</th>
            <th>EMI</th>
            <th>Principal</th>
            <th>Interest</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php
        for ($month = 1; $month <= $tenure; $month++) {
            $interestPaid = ceil($remainingLoanAmount * $monthlyRate);
            $principalPaid = ceil($emi - $interestPaid);

            // Ensure the last payment adjusts to make the remaining balance zero
            if ($month == $tenure) {
                $principalPaid = $remainingLoanAmount;
                $emi = $principalPaid + $interestPaid;
                $remainingLoanAmount = 0;
            } else {
                $remainingLoanAmount = ceil($remainingLoanAmount - $principalPaid);
            }

            $paymentDate = date("Y-m-d", $currentDate);

            echo "<tr>
                    <td>$month</td>
                    <td>$paymentDate</td>
                    <td>$emi</td>
                    <td>$principalPaid</td>
                    <td>$interestPaid</td>
                    <td>$remainingLoanAmount</td>
                  </tr>";

            if ($remainingLoanAmount <= 0) {
                break;
            }

            // Move to the next month
            $currentDate = strtotime("+1 month", $currentDate);
        }
        ?>
    </tbody>
</table>

<?php
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

</body>
</html>
