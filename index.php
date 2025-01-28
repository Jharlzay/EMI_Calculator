<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {

            background-color: #f1f1f1;
        }

        form {
            width: 300px;
            padding: 20px;
            margin: 50px;
            background-color: #fff;
            border-radius: 5px;
            display: block;
            display: inline-block;


        }

        label {
            display: block;
        }

        input[type="number"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        input[type="submit"].error {
            color: red;
            font-weight: bold;
        }

        #calculateEmi {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            font-weight: bold;
            padding: 30px;
        }


        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        th, td {
            padding: 12px 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff; /* Bootstrap blue */
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f1f9ff;
        }

        td {
            font-size: 14px;
        }

        h1, h2, p {
            text-align: center;
        }

    </style>
</head>
<body>
<form id="form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <h1> Loan EMI Calculator</h1>
    <br><br>

    <label for="principal"> Principal </label>
    <br>
    <input id="principal" type="number" name="principal" value="<?= isset($_POST['principal']) ? $_POST['principal']: '' ?>" placeholder="Enter Desired Amount">
    <br><br>

    <label for="rate"> Interest Rate </label>
    <select name="rate" id="rate">
        <option value="">Select Rate</option>
        <?php
        for ($rate = 5; $rate <= 50; $rate += 5): ?>
            <option value="<?= $rate; ?>" <?= (isset($_POST['rate']) && (int)$_POST['rate'] === $rate) ? 'selected' : ''  ?> ><?= $rate; ?>%</option>
        <?php endfor; ?>
    </select>
    <br><br>

    <label for="terms"> Loan Term</label><br>
    <select name="terms" id="terms">
        <option value="">Select Term</option>
        <?php
        for($duration = 6; $duration <= 48; $duration += 6): ?>
            <option value="<?= $duration; ?>"  <?= (isset($_POST['terms']) && (int)$_POST['terms'] === $duration) ? 'selected' : ''  ?> ><?= $duration; ?> Months</option>
        <?php endfor; ?>
    </select>
    <br><br>

    <div id="error"></div>


    <div id="calculateEmi"></div>

    <input type="submit" id="calculate" name="calculate" value="calculate">
</form>


<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $principal = filter_input(INPUT_POST, "principal");
    $rate = $_POST["rate"];
    $terms = $_POST["terms"];

    if ($principal <= 1000 || $rate <= NULL || $terms <= NULL) {
        echo "<div class='error'> Please Review Your inputs and try again</div>";
    } else {
        $monthlyRate = $rate / 12 / 100;
        $monthlyRepayment = $principal * $monthlyRate / (1 - pow(1 + $monthlyRate, -$terms));
        //$months = $terms * 12;
        //$emi = $principal * $monthlyRate * pow(1 + $monthlyRate, $months) / (pow(1 + $monthlyRate, $months) - 1);

        $remainingBalance = $principal;
        $tableData = [];
        $totalRepayment = 0;

        for ($month = 1; $month <= $terms; $month++) {
            $monthlyInterest = $remainingBalance * $monthlyRate;
            $monthlyPrincipal = $monthlyRepayment - $monthlyInterest;
            $remainingBalance -= $monthlyPrincipal;
            $totalRepayment += $monthlyRepayment;

            $tableData[] = [
                'Month' => $month,
                'Monthly Interest' => number_format($monthlyInterest, 2),
                'Principal Repayment' => number_format($monthlyPrincipal, 2),
                'Total Monthly Payment' => number_format($monthlyRepayment, 2),
                'Remaining Balance' => number_format(max($remainingBalance, 0), 2),
            ];

        }

/*
        for ($month = 1; $month <= $terms; $month++) {
            $monthlyInterest = $remainingBalance * $monthlyRate;
            $principalRepayment = $emi - $monthlyInterest;
            $remainingBalance -= $principalRepayment;

            //Add to table data
            $tableData[] = [
                'Month' => $month,
                'Monthly Interest' => $monthlyInterest,
                'Principal Repayment' => $principalRepayment,
                'Total Monthly Payment' => $emi,
                'Remaining Balance' => $remainingBalance,
            ];

        }*/

        //Display Results
        echo "<h1>Loan EMI Calculation Results</h1>";
        echo "<p> Loan Amount:" . number_format($principal, 2) . "</p>";
        echo "<p> Interest Rate:" . $rate . "% per annum.</p>";
        echo "<p> Loan Term:" . $terms . " months.</p>";
        echo "<p>Total EMI: " . number_format($totalRepayment, 2) . "</p>";

        //Display Table
        echo "<h2>Monthly Payment Breakdown</h2>";
        echo "<table class='table'>
                <tr>
                    <th>Month</th>
                    <th>Monthly Interest</th>
                    <th>Principal Repayment</th>
                    <th>Total Monthly Payment</th>
                    <th>Remaining Balance</th>
                </tr>";

        foreach ($tableData as $row) {
            echo "<tr>
                        <td>" . $row['Month'] . "</td> 
                        <td>" . $row['Monthly Interest'] . "</td>
                        <td>" . $row['Principal Repayment'] . "</td>
                        <td>" . $row['Total Monthly Payment'] . "</td>
                        <td>" . $row['Remaining Balance'] . "</td>
                        </tr>";
        }

        echo "</table>";
    }
} else {
    echo "<div class='invalid'> Invalid Request Method.</div>";
}


//echo our the EMI result


?>
<script src="index.js">


    document.getElementById('calculate').addEventListener('click', function (e) {
        e.preventDefault();
        const form = document.getElementById('form');
        form.style.display = '';


    });
</script>
</body>
</html>