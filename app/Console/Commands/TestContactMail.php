<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestContactMail extends Command
{
    protected $signature   = 'mail:test-contact {--to=rarkumar777@gmail.com : Email address to send test to}';
    protected $description = 'Test contact form SMTP mail delivery';

    public function handle()
    {
        $to = $this->option('to');

        $this->info('=== Mail SMTP Configuration ===');
        $this->line('Mailer   : ' . config('mail.default'));
        $this->line('Host     : ' . config('mail.mailers.smtp.host'));
        $this->line('Port     : ' . config('mail.mailers.smtp.port'));
        $this->line('Username : ' . config('mail.mailers.smtp.username'));
        $this->line('Encrypt  : ' . config('mail.mailers.smtp.encryption'));
        $this->line('From     : ' . config('mail.from.address'));
        $this->line('Sending test email to: ' . $to);
        $this->line('');

        try {
            $body = "<h2 style='color:#f59e0b;'>✅ Contact Form Mail Test</h2>"
                  . "<p>This is a test email from PV Travels contact form SMTP setup.</p>"
                  . "<p><strong>Server Time:</strong> " . now() . "</p>"
                  . "<p><strong>Host:</strong> " . gethostname() . "</p>"
                  . "<p>If you received this, SMTP is working correctly!</p>";

            Mail::html($body, function ($m) use ($to, $body) {
                $m->to($to, 'Test Recipient')
                  ->subject('✅ PV Travels Contact Form — SMTP Test ' . now()->format('H:i:s'));
            });

            $this->info('✅ SUCCESS! Email sent successfully to: ' . $to);
            $this->info('Please check your inbox (and spam folder).');
            Log::info('TestContactMail: Email sent to ' . $to);

        } catch (\Exception $e) {
            $this->error('❌ FAILED! Email could not be sent.');
            $this->error('Error: ' . $e->getMessage());
            $this->error('Code : ' . $e->getCode());
            Log::error('TestContactMail FAILED', [
                'error' => $e->getMessage(),
                'code'  => $e->getCode(),
            ]);
        }

        return 0;
    }
}
