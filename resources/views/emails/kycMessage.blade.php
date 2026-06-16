<!DOCTYPE html>
<html>
<head>
    <title>KYC Status Update</title>
</head>
<body>
    <h3>Dear {{ $kyc->user->first_name ?? 'Member' }}</h3>
    <p>Please note below your kyc status update:</p>
    <p>&nbsp;</p>
    <h4>Status: {{ $kyc->status ?? '' }}</h4>
    <h4>Message: {!! nl2br($kyc->status_message ?? '') !!}</h4>
    <p><br></p>
    <p>If you need any assistance, our Customer Support Team is here to help at <a href="support@necard.io">support@necard.io</a></p>
    <p>Kind regards,</p>
    <p><b>The NE Card Team</b></p>
    <p>New Era of Payment Solutions</p>
</body>
</html>
