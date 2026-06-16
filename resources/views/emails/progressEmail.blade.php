<!DOCTYPE html>
<html>
<head>
    <title>Progress Updates {{ $details['status'] ?? '' }}</title>
</head>
<body>
    <h3>Dear {{ $details['name'] ?? 'Member' }}</h3>
    <p>Please note a new progress update is availalable for you.</p>
    <p>&nbsp;</p>
    <h3>Status: {{ $details['status'] ?? '' }}</h3>
    <h4>{!! nl2br($details['details'] ?? '') !!}</h4>
    <p><br></p>
    <p>If you need any assistance, our Customer Support Team is here to help at <a href="support@necard.io">support@necard.io</a></p>
    <p>Kind regards,</p>
    <p><b>The NE Card Team</b></p>
    <p>New Era of Payment Solutions</p>
</body>
</html>
