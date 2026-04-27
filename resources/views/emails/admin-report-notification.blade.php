<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report Notification</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f3f4f6;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="100%" max-width="600px" cellpadding="0" cellspacing="0" style="max-width: 600px; width: 100%; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">

                    <!-- Header with urgent gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px 30px; text-align: center;">
                            <div style="font-size: 48px; margin-bottom: 10px;">🚨</div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 600;">New Report Received!</h1>
                            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px;">Action Required - Please Review Immediately</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Admin Greeting -->
                            <p style="font-size: 16px; color: #374151; margin-bottom: 20px;">
                                <strong style="font-size: 18px;">Hello {{ $admin->name }},</strong>
                            </p>

                            <!-- Alert Badge -->
                            <div style="background-color: #fee2e2; border-radius: 8px; padding: 12px; margin-bottom: 25px; text-align: center;">
                                <span style="color: #dc2626; font-weight: 600;">⚠️ A new report requires your attention ⚠️</span>
                            </div>

                            <!-- Student Information Card -->
                            <div style="background-color: #f0fdf4; border-radius: 12px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #22c55e;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #166534; margin: 0 0 15px 0;">
                                    👨‍🎓 Reporter Information
                                </h2>
                                <table width="100%" style="font-size: 14px;">
                                    <tr>
                                        <td style="padding: 5px 0; color: #4b5563; width: 120px;">Student Name:</td>
                                        <td style="padding: 5px 0; color: #1f2937; font-weight: 500;">{{ $student->name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #4b5563;">Student Email:</td>
                                        <td style="padding: 5px 0; color: #1f2937;">{{ $student->email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #4b5563;">Student ID:</td>
                                        <td style="padding: 5px 0; color: #1f2937;">#{{ $student->id }}</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #4b5563;">Reported At:</td>
                                        <td style="padding: 5px 0; color: #1f2937;">{{ $report->created_at->format('F j, Y g:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Report Details Card -->
                            <div style="background-color: #fef3c7; border-radius: 12px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #f59e0b;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #92400e; margin: 0 0 15px 0;">
                                    📋 Report Details
                                </h2>

                                <table width="100%" style="margin-bottom: 15px;">
                                    <tr>
                                        <td style="padding: 8px 0; width: 130px; color: #6b7280; font-size: 14px;">Report ID:</td>
                                        <td style="padding: 8px 0;">
                                            <span style="background-color: #dc2626; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600;">#{{ $report->id }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Content Type:</td>
                                        <td style="padding: 8px 0;">
                                            <span style="background-color: {{ $report->type === 'community' ? '#dbeafe' : ($report->type === 'story' ? '#fce7f3' : '#e0e7ff') }}; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                                {{ ucfirst($report->type) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Issue Type:</td>
                                        <td style="padding: 8px 0; color: #1f2937; font-weight: 500;">{{ ucfirst($report->issue) }}</td>
                                    </tr>
                                    @if($report->community_id)
                                    <tr>
                                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Community ID:</td>
                                        <td style="padding: 8px 0; color: #1f2937;">#{{ $report->community_id }}</td>
                                    </tr>
                                    @endif
                                    @if($report->hiveboards_id)
                                    <tr>
                                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">HiveBoard ID:</td>
                                        <td style="padding: 8px 0; color: #1f2937;">#{{ $report->hiveboards_id }}</td>
                                    </tr>
                                    @endif
                                    @if($report->stories_id)
                                    <tr>
                                        <td style="padding: 8px 0; color: #6b7280; font-size: 14px;">Story ID:</td>
                                        <td style="padding: 8px 0; color: #1f2937;">#{{ $report->stories_id }}</td>
                                    </tr>
                                    @endif
                                </table>

                                <!-- Description -->
                                @if($report->description)
                                <div style="background-color: #ffffff; border-radius: 8px; padding: 15px; margin-top: 10px;">
                                    <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 13px; font-weight: 500;">📝 Report Description:</p>
                                    <p style="margin: 0; color: #374151; font-size: 14px; line-height: 1.5;">{{ $report->description }}</p>
                                </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div style="margin-bottom: 25px;">
                                <h3 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-bottom: 15px;">⚡ Quick Actions:</h3>
                                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                    <a href="{{ url('/admin/reports/' . $report->id) }}" style="display: inline-block; background-color: #3b82f6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500;">🔍 View Report Details</a>
                                    <a href="{{ url('/admin/reports/' . $report->id . '/resolve') }}" style="display: inline-block; background-color: #22c55e; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500;">✅ Mark as Resolved</a>
                                    <a href="{{ url('/admin/content/' . $report->type . '/' . ($report->community_id ?? $report->hiveboards_id ?? $report->stories_id ?? $report->journal_id)) }}" style="display: inline-block; background-color: #ef4444; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 500;">🚫 Review Content</a>
                                </div>
                            </div>

                            <!-- Priority Matrix -->
                            <div style="background-color: #f3f4f6; border-radius: 8px; padding: 15px; margin-bottom: 25px;">
                                <h3 style="font-size: 14px; font-weight: 600; color: #1f2937; margin: 0 0 10px 0;">Priority Assessment:</h3>
                                <table width="100%" style="font-size: 13px;">
                                    <tr>
                                        <td style="padding: 5px 0;">Content Type:</td>
                                        <td style="padding: 5px 0;">
                                            @if(in_array($report->type, ['story', 'community']))
                                                <span style="color: #dc2626; font-weight: 600;">High Priority</span>
                                            @else
                                                <span style="color: #f59e0b;">Medium Priority</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0;">Response Time:</td>
                                        <td style="padding: 5px 0; color: #dc2626; font-weight: 600;">Within 24 hours</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Admin Instructions -->
                            <div style="background-color: #e0e7ff; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                <p style="margin: 0 0 5px 0; color: #3730a3; font-size: 13px; font-weight: 600;">📌 Admin Instructions:</p>
                                <ul style="margin: 5px 0 0 0; padding-left: 20px; color: #3730a3; font-size: 13px;">
                                    <li>Review the reported content immediately</li>
                                    <li>Verify if content violates community guidelines</li>
                                    <li>Take appropriate action (warn, hide, or remove content)</li>
                                    <li>Update report status in admin panel</li>
                                    <li>Notify student about resolution if needed</li>
                                </ul>
                            </div>

                            <!-- Stats -->
                            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                                <p style="color: #6b7280; font-size: 12px; margin: 0;">
                                    Total reports today: {{ \App\Models\Report::whereDate('created_at', today())->count() }} |
                                    Pending reports: {{ \App\Models\Report::where('status', 'pending')->count() }}
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 25px 30px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 12px;">
                                This is an automated alert from your platform's reporting system.
                            </p>
                            <p style="margin: 0; color: #9ca3af; font-size: 11px;">
                                © {{ date('Y') }} Your Platform Name. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
