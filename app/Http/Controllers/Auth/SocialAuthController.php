<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Models\UserAiProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    protected array $allowedProviders = ['google', 'facebook'];

    /**
     * Redirect to the provider's OAuth page.
     */
    public function redirectToProvider(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login')->with('error', 'Phương thức đăng nhập không được hỗ trợ.');
        }

        $clientId = config("services.{$provider}.client_id");
        if (empty($clientId)) {
            $providerName = ucfirst($provider);
            return redirect()->route('login')->with('error', "Chưa cấu hình {$providerName} OAuth trong file .env. Vui lòng thêm {$providerName}_CLIENT_ID và {$providerName}_CLIENT_SECRET.");
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Throwable $e) {
            Log::error("Socialite redirect error for {$provider}: " . $e->getMessage());
            return redirect()->route('login')->with('error', "Không thể kết nối đến {$provider}. Vui lòng thử lại sau.");
        }
    }

    /**
     * Handle the callback returned from the provider.
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        if (!in_array($provider, $this->allowedProviders, true)) {
            return redirect()->route('login')->with('error', 'Phương thức đăng nhập không được hỗ trợ.');
        }

        try {
            /** @var \Laravel\Socialite\Contracts\User $socialUser */
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::error("Socialite callback error for {$provider}: " . $e->getMessage());
            return redirect()->route('login')->with('error', "Đăng nhập bằng {$provider} thất bại hoặc đã bị hủy.");
        }

        $providerId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Khách Hàng');
        $avatar = $socialUser->getAvatar();

        if (empty($email) && empty($providerId)) {
            return redirect()->route('login')->with('error', "Không thể xác thực danh tính từ {$provider}.");
        }

        $user = DB::transaction(function () use ($provider, $providerId, $email, $name, $avatar, $socialUser) {
            // 1. Check if social account link already exists
            $account = SocialAccount::where('provider', $provider)
                ->where('provider_id', $providerId)
                ->first();

            if ($account) {
                // Update tokens & avatar if available
                $account->update([
                    'avatar_url' => $avatar,
                    'access_token' => $socialUser->token ?? null,
                    'refresh_token' => $socialUser->refreshToken ?? null,
                ]);
                return $account->user;
            }

            // 2. Find user by email or create a new user
            $isNew = false;
            $user = null;
            if (!empty($email)) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $isNew = true;
                $user = User::create([
                    'name' => $name,
                    'email' => $email ?: "{$provider}_{$providerId}@social.hihishop.vn",
                    'password' => Hash::make(Str::random(32)),
                    'avatar' => $avatar,
                    'email_verified_at' => now(),
                    'customer_tier' => 'bronze',
                    'is_active' => true,
                ]);

                // Create initial AI Persona Profile for new user
                UserAiProfile::create([
                    'user_id' => $user->id,
                    'preferred_categories' => [],
                    'preferred_brands' => [],
                    'budget_range' => 'undecided',
                    'style_persona' => 'Khách hàng mới',
                    'interaction_count' => 0,
                    'last_analyzed_at' => now(),
                ]);

                event(new Registered($user));
            } elseif (empty($user->avatar) && !empty($avatar)) {
                $user->update(['avatar' => $avatar]);
            }

            // 3. Link social account
            SocialAccount::create([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_id' => $providerId,
                'avatar_url' => $avatar,
                'access_token' => $socialUser->token ?? null,
                'refresh_token' => $socialUser->refreshToken ?? null,
            ]);

            return $user;
        });

        Auth::login($user, true);
        request()->session()->regenerate();

        $providerLabel = ucfirst($provider);
        return redirect()->intended(route('home'))
            ->with('success', "Đăng nhập bằng {$providerLabel} thành công! Chào mừng {$user->name} đến với HIHI Shop.");
    }
}

