<?php

namespace App\Livewire;

use App\Mail\RepairRequest;
use App\Models\Setting;
use App\Support\RepairServicesContent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

class RepairServices extends Component
{
    public string $name = '';
    public string $phone = '';
    public string $deviceType = '';
    public string $description = '';

    #[Locked]
    public bool $submitted = false;

    public function submit(): void
    {
        if ($this->submitted) {
            return;
        }

        $this->name = trim($this->name);
        $this->phone = preg_replace('/[\s.-]+/', '', trim($this->phone));
        $this->description = trim($this->description);
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'regex:/^(?:0[0-9]{9,10}|\+84[0-9]{9,10})$/'],
            'deviceType' => ['required', 'in:keyboard,mouse,headphones'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên.',
            'name.max' => 'Họ tên không quá 120 ký tự.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Vui lòng nhập số điện thoại hợp lệ.',
            'deviceType.required' => 'Vui lòng chọn loại thiết bị.',
            'deviceType.in' => 'Vui lòng chọn loại thiết bị trong danh sách.',
            'description.required' => 'Vui lòng mô tả lỗi thiết bị.',
            'description.min' => 'Vui lòng mô tả lỗi ít nhất 10 ký tự.',
            'description.max' => 'Mô tả không quá 3.000 ký tự.',
        ]);

        $key = 'babu-care:'.hash('sha256', session()->getId().'|'.request()->ip());
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->addError('request', 'Bạn đã gửi nhiều yêu cầu. Vui lòng thử lại sau vài phút hoặc gọi hotline.');
            return;
        }

        $lock = Cache::lock($key.':sending', 30);
        if (! $lock->get()) {
            $this->addError('request', 'Yêu cầu đang được gửi. Vui lòng chờ một chút.');
            return;
        }

        try {
            $recipient = trim((string) Setting::query()->value('email'));
            if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                $this->addError('request', 'Kênh nhận yêu cầu tạm chưa sẵn sàng. Vui lòng gọi hotline của shop.');
                return;
            }

            $fingerprint = $key.':'.hash('sha256', json_encode($data));
            if (! Cache::has($fingerprint)) {
                RateLimiter::hit($key, 600);
                Mail::to($recipient)->send(new RepairRequest($data));
                Cache::put($fingerprint, true, 600);
            }

            $this->reset(['name', 'phone', 'deviceType', 'description']);
            $this->resetValidation();
            $this->submitted = true;
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('request', 'Chưa gửi được yêu cầu. Thông tin vẫn được giữ lại; bạn có thể thử lại hoặc gọi hotline.');
        } finally {
            $lock->release();
        }
    }

    public function render()
    {
        return view('repair-services', [
            'groups' => RepairServicesContent::groups(),
            'faqs' => RepairServicesContent::faqs(),
            'repairSettings' => Setting::query()->first(),
        ]);
    }
}
