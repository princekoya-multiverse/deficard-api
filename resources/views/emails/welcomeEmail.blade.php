<!DOCTYPE html>
<html>
<head>
    <title>Welcome to New Era Payment Solutions</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Open Sans', 'Poppins', sans-serif; font-size: 14px">
    <table width="700" border="0" style="max-width: 700px">
        <tr>
            <td><img src="https://necard.io/frontend_assets/assets/images/Home-Page/Header-Logo.png" alt="" border="0"></td>
        </tr>
        <tr>
            <td><hr style="color:#ffc107"></td>
        </tr>
        <tr>
            <td>
                <h3>Dear {{ $user->first_name .' ' . $user->last_name}},</h3>
                <p>Welcome to <b>NE Card</b> — the <b>New Era of Payment Solutions</b>. We’re delighted to have you with us.</p>
                <p>Your NE Card gives you access to secure, fast, and seamless transactions, powered by innovative technologies like blockchain and AI.</p>
                <p>Get started today by ordering your card!</p>
                <p>If you need any assistance, our Customer Support Team is here to help at <a href="support@necard.io">support@necard.io</a></p>
                <p>Thank you for choosing NE Card. We look forward to serving you.</p>
                <p><br></p>
                <p>Kind regards,</p>
                <p><b>The NE Card Team</b>
                <br>New Era of Payment Solutions</p>
            </td>
        </tr>
    </table>
</body>
</html>
