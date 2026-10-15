<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Task Assigned</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
            margin: 0;
            padding: 24px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: #ffffff;
            padding: 32px 28px;
            text-align: left;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        .header p {
            margin: 6px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 28px;
        }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 16px;
        }
        .task-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }
        .task-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }
        .meta-row {
            display: flex;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .meta-label {
            font-weight: 600;
            color: #64748b;
            width: 120px;
        }
        .meta-value {
            color: #1e293b;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-urgent { background-color: #fee2e2; color: #b91c1c; }
        .badge-high { background-color: #ffedd5; color: #c2410c; }
        .badge-medium { background-color: #fef3c7; color: #b45309; }
        .badge-low { background-color: #e0f2fe; color: #0369a1; }
        .description-box {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed #cbd5e1;
            font-size: 14px;
            line-height: 1.6;
            color: #334155;
        }
        .btn-wrapper {
            text-align: center;
            margin: 28px 0;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff !important;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px 28px;
            border-top: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Task Management System</h1>
            <p>Notification of Task Assignment</p>
        </div>
        <div class="content">
            <p class="greeting">Hello {{ $staff->name }},</p>
            <p>You have been assigned a new task by {{ $task->creator->name ?? 'Administrator' }}. Details are provided below:</p>
            
            <div class="task-card">
                <div class="task-title">{{ $task->title }}</div>
                
                <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #64748b; width: 110px;">Project:</td>
                        <td style="padding: 6px 0; font-weight: 600; color: #0f172a;">{{ $task->project->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Priority:</td>
                        <td style="padding: 6px 0;">
                            <span class="badge badge-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Due Date:</td>
                        <td style="padding: 6px 0; color: #0f172a;">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No deadline' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 6px 0; font-weight: 600; color: #64748b;">Status:</td>
                        <td style="padding: 6px 0; color: #0f172a; text-transform: capitalize;">{{ str_replace('_', ' ', $task->status) }}</td>
                    </tr>
                </table>

                @if($task->description)
                <div class="description-box">
                    <strong>Description:</strong><br>
                    {{ $task->description }}
                </div>
                @endif
            </div>

            <div class="btn-wrapper">
                <a href="{{ url('/staff/tasks') }}" class="btn">View Task in Staff Portal</a>
            </div>

            <p style="font-size: 13px; color: #64748b; margin-top: 24px;">
                Please ensure your account remains active and verified to update the task status in the portal.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Task Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
