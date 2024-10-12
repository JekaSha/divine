<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transaction Status Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .email-header {
            text-align: center;
            padding-bottom: 20px;
        }
        .email-header h1 {
            margin: 0;
            color: #333333;
        }
        .email-content {
            color: #333333;
        }
        .email-footer {
            text-align: center;
            margin-top: 30px;
            color: #888888;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="email-header">
        <h1>Transaction Status Update</h1>
    </div>

    <div class="email-content">
        <p>Hello,</p>
        <p>We would like to inform you that the status of your transaction has been updated.</p>

        <table>
            <tr>
                <td><strong>Transaction ID:</strong></td>
                <td>{{ $transaction->id }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td>{{ ucfirst($transaction->status) }}</td>
            </tr>
            <tr>
                <td><strong>Amount:</strong></td>
                <td>{{ $transaction->amount }} {{ $transaction->wallet->currency->name }}</td>
            </tr>
            <tr>
                <td><strong>Exchange Rate:</strong></td>
                <td>{{ $transaction->exchange_rate }}</td>
            </tr>
            <tr>
                <td><strong>Wallet Address:</strong></td>
                <td>{{ $transaction->wallet->wallet_token }}</td>
            </tr>
            <tr>
                <td><strong>Expiry Time:</strong></td>
                <td>{{ $transaction->expiry_time }}</td>
            </tr>
        </table>

        <p>If you have any questions or concerns, please do not hesitate to contact our support team.</p>

        <a href="https://yourwebsite.com/support" class="button">Contact Support</a>
    </div>

    <div class="email-footer">
        <p>Thank you for using our service.</p>
        <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
    </div>
</div>
</body>
</html>
