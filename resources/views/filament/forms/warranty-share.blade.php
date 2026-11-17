<div x-data="{ copied: false }" style="display:grid;gap:14px">
    <label>Link kích hoạt<textarea readonly rows="4" style="width:100%;color:#111;background:#fff;border-radius:8px;padding:12px" onclick="this.select()">{{ $getState()['link'] ?? '' }}</textarea></label>
    <label>Tin nhắn gửi khách<textarea readonly rows="6" style="width:100%;color:#111;background:#fff;border-radius:8px;padding:12px" onclick="this.select()">{{ $getState()['message'] ?? '' }}</textarea></label>
    <x-filament::button type="button" x-on:click="navigator.clipboard.writeText(@js($getState()['message'] ?? '')).then(() => copied = true).catch(() => { copied = false; })"><span x-text="copied ? 'Đã sao chép' : 'Sao chép tin nhắn và link'"></span></x-filament::button>
    <p>Nếu trình duyệt không cho sao chép, bấm vào ô và dùng Ctrl+C.</p>
</div>
