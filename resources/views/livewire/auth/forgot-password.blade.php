<div>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">نسيت كلمة المرور؟</h2>
        <p class="text-sm text-gray-500 mt-2">أدخل بريدك الإلكتروني وسنرسل لك رابطاً لإعادة التعيين</p>
    </div>

    @if($statusMessage)
        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg text-sm font-bold text-center">
            {{ $statusMessage }}
        </div>
    @endif

    <form wire:submit.prevent="sendResetLink" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
            <input type="email" wire:model="email" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="example@erp.com">
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
            إرسال رابط الاستعادة
        </button>
    </form>

    <div class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-bold hover:underline">
            &rarr; العودة لتسجيل الدخول
        </a>
    </div>
</div>
