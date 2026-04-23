<div>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">تسجيل الدخول</h2>
        <p class="text-sm text-gray-500 mt-2">أدخل بياناتك للوصول إلى لوحة التحكم</p>
    </div>

    <form wire:submit.prevent="login" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
            <input type="email" wire:model="email" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">كلمة المرور</label>
            <input type="password" wire:model="password" class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center">
                <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-blue-600">
                <span class="ml-2 text-sm text-gray-600 px-2">تذكرني</span>
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">نسيت كلمة المرور؟</a>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition">
            دخول
        </button>
    </form>

    <div class="mt-6 text-center text-sm">
        <p class="text-gray-600">ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline">إنشاء حساب جديد</a></p>
    </div>
</div>
