<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReportNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $report;
    public $admin;

    public function __construct($report, $admin)
    {
        $this->report = $report;
        $this->admin = $admin;
    }

    public function build()
    {
        return $this->subject('🚨 New Report Alert - Action Required')
                    ->view('emails.admin-report-notification')
                    ->with([
                        'report' => $this->report,
                        'admin' => $this->admin,
                    ]);
    }
}
