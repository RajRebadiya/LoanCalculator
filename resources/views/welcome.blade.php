<!DOCTYPE html>
<html>
<head>
    <title>Loan EMI Calculation</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

    <div class='invoice-wrapper'>
        <img src='' alt='logo' class='invoice-logo'>
        <div class='invoice-company'>
            <h2 style="font-size: xx-large" class='invoice-company-name text-center'>Loan EMI Calculator</h2>
            
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
    function fetchLoanDataFromAPI() {
        // Example function to fetch data from API
        // Replace this with your actual code to fetch data from the API
        // For demonstration, returning static data
        return [
            'loan_amount' => 50000, // Example: Loan Amount in rupees
            'interest_rate' => 1.25, // Example: Interest Rate in percentage
            'tenure' => 24, // Example: Tenure in months
            'start_date' => date("Y-m-d") // Example: Start Date (current date)
        ];
    }

    // Fetch loan data from API
    $data = fetchLoanDataFromAPI();

    // Dynamic data from API
    $loanAmount = $data['loan_amount'];
    $interestRate = $data['interest_rate'];
    $tenure = $data['tenure'];
    $startDate = $data['start_date'];

    // Calculate EMI
    $emi = calculateEMI($loanAmount, $interestRate, $tenure);

    $remainingLoanAmount = $loanAmount;
    $monthlyRate = $interestRate / (12 * 100);
    $currentDate = strtotime($startDate);
?>

<table>
    <thead>
        <tr>
            <th>Month</th>
            <th>Date</th>
            <th>EMI</th>
            <th>Principal Paid</th>
            <th>Interest Paid</th>
            <th>Remaining Loan Amount</th>
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
