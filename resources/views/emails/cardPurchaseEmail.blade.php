<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.bunny.net/css?family=Poppins" rel="stylesheet">
</head>
<body style="font-family: Poppins,Arial,Helvetica,sans-serif; font-size: 14px">

    <p style="background: #ffffff; border-bottom: 2px solid #F0F0F0;margin-bottom: 1em">
        <img src="https://necard.io/frontend_assets/assets/images/Home-Page/Header-Logo.png" alt="NE Card">
    </p>

    <h3>Dear Member, Your NE Card purchase payment transaction has been received successfully.</h3>

    <p>Please note the details below:</p>

    <table width="500" style="width:500px" cellpadding="2" cellspacing="2" style="border-collapse: collapse">
        <tr>
            <td align="right"><b>Payment Type</b></td>
            <td style="background-color: #f0f0f0">{{ ucfirst($payment->type) }}</td>
        </tr>
        <tr>
            <td align="right"><b>Card Type</b></td>
            <td style="background-color: #f0f0f0">{{ ucfirst($payment->card_type) }}</td>
        </tr>
        <tr>
            <td align="right"><b>USDT Address</b></td>
            <td style="background-color: #f0f0f0">{{ $payment->trans_address }}</td>
        </tr>
        <tr>
            <td align="right"><b>Status</b></td>
            <td style="background-color: #f0f0f0">{{ ucfirst($payment->trans_status) }}</td>
        </tr>
        <tr>
            <td align="right"><b>Amount</b></td>
            <td style="background-color: #f0f0f0">{{ $payment->trans_amount }} USDT</td>
        </tr>
        @if($payment->trans_fee > 0)
        <tr>
            <td align="right"><b>Fee</b></td>
            <td style="background-color: #f0f0f0">{{ $payment->trans_fee }} USDT</td>
        </tr>
        @endif
        <tr>
            <td align="right"><b>Date</b></td>
            <td style="background-color: #f0f0f0">{{ $payment->created_at->format('d/m/Y') }}</td>
        </tr>
    </table>
    <p><br></p>
    <p>Thank you for choosing NE Card. We look forward to serving you.</p>
    <p>If you need any assistance, our Customer Support Team is here to help at <a href="support@necard.io">support@necard.io</a></p>
    <p>Kind regards,</p>
    <p><b>The NE Card Team</b>
    <br>New Era of Payment Solutions</p>

</body>
</html>
