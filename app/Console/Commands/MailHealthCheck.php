<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MailHealthCheck extends Command
{
    protected $signature = 'hihi:mail-check {email? : Địa chỉ nhận email thử}';

    protected $description = 'Kiểm tra cấu hình email (SMTP) và gửi thử một email';

    public function handle(): int
    {
        $mailer = config('mail.default');
        $config = config("mail.mailers.{$mailer}", []);
        $from = config('mail.from');

        $this->line('Mailer : <comment>'.$mailer.'</comment>');
        $this->line('Host   : '.($config['host'] ?? '-').':'.($config['port'] ?? '-'));
        $this->line('Từ     : '.($from['address'] ?? '-').' ('.($from['name'] ?? '-').')');

        if ($mailer === 'log') {
            $this->newLine();
            $this->warn('MAIL_MAILER=log → email chỉ ghi vào storage/logs, KHÔNG gửi tới khách.');
            $this->line('Đặt MAIL_MAILER=smtp + MAIL_HOST / MAIL_PORT / MAIL_USERNAME / MAIL_PASSWORD để gửi thật.');
            $this->line('Khách bấm "Quên mật khẩu" sẽ không nhận được mail cho tới khi cấu hình xong.');
        }

        $email = $this->argument('email');

        if (!$email) {
            $this->newLine();
            $this->line('Gửi thử: php artisan hihi:mail-check ban@example.com');

            return self::SUCCESS;
        }

        try {
            Mail::raw(
                'Đây là email kiểm tra cấu hình từ HIHI Shop lúc '.now()->format('d/m/Y H:i:s').'. Nếu bạn nhận được mail này, cấu hình SMTP đã đúng.',
                function ($message) use ($email) {
                    $message->to($email)->subject('[HIHI Shop] Kiểm tra cấu hình email');
                }
            );
        } catch (\Throwable $e) {
            $this->error('Gửi thất bại: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info('✔ Đã gửi email thử tới '.$email.' — kiểm tra cả hộp thư Spam/Quảng cáo.');

        return self::SUCCESS;
    }
}
