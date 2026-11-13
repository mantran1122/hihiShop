<?php

namespace App\Console\Commands;

use App\Models\Review;
use App\Models\User;
use Illuminate\Console\Command;

class ClearDemoReviews extends Command
{
    protected $signature = 'hihi:demo-reviews-clear {--purge-users : Xoá luôn các tài khoản đánh giá mẫu}';

    protected $description = 'Xoá các đánh giá mẫu (tài khoản *@*.demo@hihishop.vn) trước khi bán thật';

    public function handle(): int
    {
        $userIds = User::query()
            ->where('email', 'like', '%.demo@hihishop.vn')
            ->pluck('id');

        if ($userIds->isEmpty()) {
            $this->info('Không tìm thấy tài khoản đánh giá mẫu nào.');

            return self::SUCCESS;
        }

        $deleted = Review::query()->whereIn('user_id', $userIds)->delete();
        $this->info("Đã xoá {$deleted} đánh giá mẫu.");

        if ($this->option('purge-users')) {
            $users = User::query()->whereIn('id', $userIds)->delete();
            $this->info("Đã xoá {$users} tài khoản đánh giá mẫu.");
        }

        return self::SUCCESS;
    }
}
