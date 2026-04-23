<div>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">إعادة تعيين كلمة المرور</h2>
        <p class="text-sm text-gray-500 mt-2">أدخل كلمة المرور الجديدة الخاصة بك</p>
    </div>

    <form wire:submit.prevent="resetPassword" class="space-y-4">
        <input type="hidden" wire:model="token">

        <div>
            <label class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
            <input type="email" wire:model="email" class="mt-1 w-full p-3 border border-gray-300 rounded-lg bg-gray-100" readonly>
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">كلمة المرور الجديدة</label>
            <input type="password" wire:model="password" class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
            <input type="password" wire:model="password_confirmation" class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
        </div>

        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition">
            حفظ كلمة المرور الجديدة
        </button>
    </form>
</div>
