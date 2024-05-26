<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Loan Calculator</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf/0.9.2/html2pdf.bundle.min.js"></script>
    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
           background: #ece3ff;

            width: 159px;
            text-align: center;
            height: 54px;
            font-size: 16px;
            margin-bottom: 12px;
            font-weight: 600;
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
            background-color: #d2bdff6b;
        }


        th, td {
            width: calc((600px - 3rem) / 4);
            text-align: center;
            padding: 0.75rem;
        }

        tr:nth-of-type(1) {
            background-color: rgba(0, 0, 0, 0.2);
        }

        tr:nth-of-type(2), tr:nth-of-type(3) {
            border-bottom: 0.5px solid rgba(0, 0, 0, 0.25);
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
        #emi-table-body tr:nth-child(odd) {
          background: #D2BDFF;
        }
        thead tr{
          background: #936be96b;
          color: white;
        }
    </style>
</head>
<body>
    <main class='invoice' id='invoice'>
        <div class='invoice-wrapper'>
            <!-- Your header content here -->
        </div>
        <div class='invoice-wrapper'>
            @foreach ($loans as $loan)
            <div class='invoice-details'>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'>{{$loan->loan_amount}} <br><span class='strong'>Loan Amount</span></span>
                    <span class='invoice-info-key box'>{{$loan->emi}} <br><span class='strong'>EMI</span></span>
                </div>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'>{{$loan->i_rate}} <br><span class="strong">Interest Rate(%)</span></span>
                    <span class='invoice-info-key box'>{{$loan->total_inter}} <br><span class="strong">Total Interest</span></span>
                </div>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'>{{$loan->tenure}}<br><span class="strong">Tenure(In Month)</span></span>
                    <span class='invoice-info-key box'>{{$loan->total_amount}} <br><span class="strong" style="font-size: 14px;">Total Payable Amount</span></span>
                </div>
                <div class='invoice-info'>
                    <span class='invoice-info-key box'>{{$loan->s_date}} <br><span class="strong">Start Date</span></span>
                    <span class='invoice-info-key box'>{{$loan->e_date}} <br><span class="strong">End Date</span></span>
                </div>
            </div>
            @endforeach
        </div>
        <table class='invoice-table'>
            <!-- Your table content here -->
        </table>
        <ion-icon style='display: none' name="prit-outline" class='invoice-print'></ion-icon>
    </main>

    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        const print = document.querySelector(".invoice-print");
        const media = window.matchMedia("print");

        const update = (e) => (print.style.display = e.matches ? "none" : "block");

        function convert() {
            media.addEventListener("change", update, false);
            window.print();
        }

        print.addEventListener("click", convert, false);

        // Function to format date
        function formatDate(date) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Intl.DateTimeFormat('en-US', options).format(date).toUpperCase();
        }

        // Function to calculate EMI details
        function calculateEmiDetails(amount, rate, tenure, startDate) {
            const emiTableBody = document.getElementById('emi-table-body');
            let currentDate = new Date(startDate);
            const monthlyRate = rate / (12 * 100);
            const emi = (amount * monthlyRate * Math.pow(1 + monthlyRate, tenure)) / (Math.pow(1 + monthlyRate, tenure) - 1);
            let balance = amount;

            for (let i = 1; i <= tenure; i++) {
                const interest = balance * monthlyRate;
                const principalComponent = emi - interest;
                balance -= principalComponent;

                const row = document.createElement('tr');
                row.classList.add('no-page-break');
                row.innerHTML = `
                    <td>${i}</td>
                    <td>${formatDate(currentDate)}</td>
                    <td>${Math.round(emi)}</td>
                    <td>${Math.round(principalComponent)}</td>
                    <td>${Math.round(interest)}</td>
                    <td>${balance > 0 ? Math.round(balance) : '0'}</td>
                `;

                emiTableBody.appendChild(row);

                currentDate.setMonth(currentDate.getMonth() + 1);
            }
        }

        // Assuming $loans is an array of loan objects passed from the controller
        @foreach ($loans as $loan)
            calculateEmiDetails({{$loan->loan_amount}}, {{$loan->i_rate}}, {{$loan->tenure}}, '{{$loan->s_date}}');
        @endforeach

        // Function to download PDF
        function downloadPDF() {
            const element = document.getElementById('invoice');
            html2pdf()
                .from(element)
                .set({
                    margin: [2, 1, 2, 5],
                    filename: 'LoanCal.pdf', // Default file name
                    html2canvas: { scale: 2 },
                    jsPDF: { orientation: 'portrait' }
                })
                .save(); // Save the PDF
        }
        // Trigger PDF download on page load
        downloadPDF();
    </script>
</body>
</html>
