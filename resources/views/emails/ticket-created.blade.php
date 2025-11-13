<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Ticket Created</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; border-radius: 8px; padding: 30px; margin-bottom: 20px;">
        <h1 style="color: #1e293b; margin-top: 0;">New Ticket Created</h1>
        
        <p>Hello <strong>{{ $ticket->assignedUser ? $ticket->assignedUser->name : 'Team' }}</strong>,</p>
        
        <p>A new support ticket has been created and requires your attention.</p>
        
        <div style="background-color: #ffffff; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <h2 style="margin-top: 0; color: #1e293b; font-size: 18px;">Ticket Details</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; font-weight: bold; width: 30%;">Ticket #:</td>
                    <td style="padding: 8px 0;">{{ $ticket->ticket_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Subject:</td>
                    <td style="padding: 8px 0;">{{ $ticket->subject }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Priority:</td>
                    <td style="padding: 8px 0;">{{ ucfirst($ticket->priority) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Status:</td>
                    <td style="padding: 8px 0;">{{ ucfirst($ticket->status) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Created by:</td>
                    <td style="padding: 8px 0;">{{ $ticket->user->name }} ({{ $ticket->user->email }})</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; font-weight: bold;">Category:</td>
                    <td style="padding: 8px 0;">{{ $ticket->category->name ?? 'Uncategorized' }}</td>
                </tr>
            </table>
        </div>
        
        <h3 style="color: #1e293b;">Description</h3>
        <p style="background-color: #ffffff; padding: 15px; border-radius: 4px; margin: 15px 0;">
            {{ Str::limit(strip_tags($ticket->description), 200) }}
        </p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $ticketUrl }}" style="display: inline-block; background-color: #3b82f6; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 6px; font-weight: bold;">
                View Ticket
            </a>
        </div>
    </div>
    
    <div style="text-align: center; color: #64748b; font-size: 14px;">
        <p>Thanks,<br>{{ config('app.name') }} Support Team</p>
    </div>
</body>
</html>
