<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Laravel\Socialite\Facades\Socialite;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Login extends Component
{
    use LivewireAlert;

    public string $activeTab = 'login'; // 'login' | 'register' | 'forgot'

    // Login fields
    public string $email = '';
    public string $password = '';
    public bool $remember = true;
    public bool $showPassword = false;

    // Register fields
    public string $reg_name = '';
    public string $reg_phone = '';
    public string $reg_email = '';
    public string $reg_password = '';
    public bool $showRegPassword = false;

    // Forgot fields
    public string $forgot_email = '';
    public bool $forgot_sent = false;

    protected $queryString = [
        'activeTab' => ['except' => 'login', 'as' => 'tab'],
    ];

    public function mount(): void
    {
        $tab = request()->query('tab');
        if (in_array($tab, ['login', 'register', 'forgot'], true)) {
            $this->activeTab = $tab;
        }

        // Fill prefill if present
        $prefillEmail = request()->query('email');
        if ($prefillEmail && filter_var($prefillEmail, FILTER_VALIDATE_EMAIL)) {
            $this->email = $prefillEmail;
        }
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['login', 'register', 'forgot'], true)) {
            $this->activeTab = $tab;
            $this->resetErrorBag();
            $this->forgot_sent = false;
        }
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $findUser = User::where('email', $user->email)->first();

            if ($findUser) {
                Auth::login($findUser, true);
                $this->alert('success', 'Đăng nhập Google thành công!', [
                    'timer' => 2000,
                    'toast' => true,
                ]);
                return redirect()->intended('/account');
            } else {
                $newUser = User::create([
                    'name' => $user->name ?: 'Khách hàng',
                    'email' => $user->email,
                    'password' => Hash::make(uniqid('babu_', true)),
                    'is_admin' => false,
                ]);

                Auth::login($newUser, true);
                $this->alert('success', 'Đăng ký thành công với tài khoản Google!', [
                    'timer' => 2500,
                    'toast' => true,
                ]);
                return redirect()->to('/account');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Không thể kết nối với Google hoặc bạn đã huỷ đăng nhập.', [
                'timer' => 3000,
                'toast' => true,
            ]);
            return redirect()->to('/account/login');
        }
    }

    public function login()
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $credentials = [
            'email' => trim($this->email),
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            session()->regenerate();
            $this->alert('success', 'Đăng nhập thành công! Chào mừng bạn quay lại.', [
                'timer' => 2000,
                'toast' => true,
            ]);
            return redirect()->intended('/account');
        }

        $this->addError('email', 'Email hoặc mật khẩu không chính xác.');
        $this->alert('error', 'Email hoặc mật khẩu không đúng!', [
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function loginDemoUser()
    {
        abort_unless(app()->environment('local', 'testing'), 404);

        $demoUser = User::firstOrCreate(
            ['email' => 'hgdigital.vn@gmail.com'],
            [
                'name' => 'Nguyễn Hoàng Nam',
                'phone' => '0988888888',
                'password' => Hash::make('12345678'),
                'is_admin' => false,
            ]
        );

        Auth::login($demoUser, true);
        session()->regenerate();

        $this->alert('success', 'Đăng nhập tài khoản mẫu thành công!', [
            'timer' => 2000,
            'toast' => true,
        ]);

        return redirect()->to('/account');
    }

    public function register()
    {
        $cleanEmail = strtolower(trim($this->reg_email));

        if ($cleanEmail === 'admin@gmail.com') {
            $this->addError('reg_email', 'Email này được bảo lưu cho quản trị hệ thống.');
            $this->alert('error', 'Email này được bảo lưu cho quản trị hệ thống!', [
                'timer' => 3000,
                'toast' => true,
            ]);
            return;
        }

        $this->validate([
            'reg_name' => ['required', 'string', 'min:2', 'max:120'],
            'reg_phone' => [
                'required',
                'string',
                'regex:/^(0|\+?84)(3|5|7|8|9)[0-9]{8}$/',
            ],
            'reg_email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'reg_password' => ['required', 'string', 'min:6'],
        ], [
            'reg_name.required' => 'Vui lòng nhập họ và tên.',
            'reg_name.min' => 'Họ và tên quá ngắn.',
            'reg_phone.required' => 'Vui lòng nhập số điện thoại.',
            'reg_phone.regex' => 'Số điện thoại không đúng định dạng (VD: 0912345678).',
            'reg_email.required' => 'Vui lòng nhập địa chỉ email.',
            'reg_email.email' => 'Email không đúng định dạng.',
            'reg_email.unique' => 'Email này đã được đăng ký. Bạn hãy đăng nhập nhé.',
            'reg_password.required' => 'Vui lòng nhập mật khẩu.',
            'reg_password.min' => 'Mật khẩu phải có tối thiểu 6 ký tự.',
        ]);

        try {
            $user = User::create([
                'name' => trim($this->reg_name),
                'phone' => trim($this->reg_phone),
                'email' => $cleanEmail,
                'password' => Hash::make($this->reg_password),
                'is_admin' => false,
            ]);

            Auth::login($user, true);
            session()->regenerate();

            $this->alert('success', 'Đăng ký tài khoản thành công!', [
                'timer' => 2000,
                'toast' => true,
            ]);

            return redirect()->to('/account');
        } catch (\Exception $e) {
            $this->alert('error', 'Đăng ký không thành công! Vui lòng thử lại.', [
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

    public function sendPasswordReset(): void
    {
        $this->validate([
            'forgot_email' => ['required', 'email'],
        ], [
            'forgot_email.required' => 'Vui lòng nhập địa chỉ email.',
            'forgot_email.email' => 'Địa chỉ email không đúng định dạng.',
        ]);

        $this->forgot_sent = true;
        $this->alert('success', 'Yêu cầu khôi phục mật khẩu đã được gửi!', [
            'timer' => 3000,
            'toast' => true,
        ]);
    }

    public function render()
    {
        return view('livewire.login');
    }
}
