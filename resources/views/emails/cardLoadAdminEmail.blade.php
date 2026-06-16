<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.bunny.net/css?family=Poppins" rel="stylesheet">
</head>
<body style="font-family: Poppins,Arial,Helvetica,sans-serif; font-size: 14px">

    <p style="background: #ffffff; border-bottom: 2px solid #F0F0F0;margin-bottom: 1em">
        <img src="https://necard.io/frontend_assets/assets/images/Home-Page/Header-Logo.png" alt="NE Card">
    </p>

    <h3>Dear Admin,</h3>
    <h4>A new payment was transfered successfully.</h4>
    <p><strong>Please note the details below:</strong></p>

    <table width="500" style="width:500px" cellpadding="2" cellspacing="2" style="border-collapse: collapse">
        <tr>
            <td align="right"><b>User Name:</b></td>
            <td style="background-color: #f0f0f0">{{ ucfirst($payment->user?->name ?? '') }}</td>
        </tr>
        <tr>
            <td align="right"><b>User Email:</b></td>
            <td style="background-color: #f0f0f0">{{ ucfirst($payment->user?->email ?? '') }}</td>
        </tr>
        <tr>
            <td align="right"><b>Payment Type</b></td>
            <td style="background-color: #f0f0f0">{{ ucfirst($payment->type) }}</td>
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
    <p>Kind regards,</p>
    <p><b>The NE Card Team</b>
    <br>New Era of Payment Solutions</p>

</body>
</html>
