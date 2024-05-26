<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loan PDF</title>
    <link rel="stylesheet" href="">
</head>
<body>
    <main class='invoice' id='invoice'>
        <div class='invoice-wrapper'>
            <img src='{{ asset('logo.png') }}' alt='logo' class='invoice-logo'>
            <div class='invoice-company'>
                <h2 style="font-size: xx-large" class='invoice-company-name text-center'>Loan EMI Calculator</h2>
            </div>
        </div>
        <div class='invoice-wrapper'>
            <div class='invoice-details'>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'> <br><span class='strong'>Loan Amount</span></span>
                    <span class='invoice-info-key box'><br><span class='strong'>EMI</span></span>
                </div>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'> <br><span class="strong">Interest Rate(%)</span></span>
                    <span class='invoice-info-key box'> <br><span class="strong">Total Interest</span></span>
                </div>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'><br><span class="strong">Tenure(In Month)</span></span>
                    <span class='invoice-info-key box'><br><span class="strong" style="font-size: 14px;">Total Payable Amount</span></span>
                </div>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'><br><span class="strong">Start Date</span></span>
                    <span class='invoice-info-key box'><br><span class="strong">End Date</span></span>
                </div>
            </div>
        </div>
        <table class='invoice-table'>
            <thead>
                <tr style='background: #56447e;'>
                    <th>NUMBER</th>
                    <th>EMI DATE</th>
                    <th>EMI</th>
                    <th>PRINCIPAL</th>
                    <th>INTEREST</th>
                    <th>BALANCE</th>
                </tr>
            </thead>
            <tbody id="emi-table-body">
               
            </tbody>
        </table>
    </main>
</body>
</html>
