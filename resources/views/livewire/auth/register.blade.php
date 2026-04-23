<div>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">إنشاء حساب موظف</h2>
        <p class="text-sm text-gray-500 mt-2">انضم إلى فريق العمل الآن</p>
    </div>

    <form wire:submit.prevent="register" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">الاسم الكامل</label>
            <input type="text" wire:model="name" class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
            <input type="email" wire:model="email" class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">كلمة المرور</label>
            <input type="password" wire:model="password" class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
            <input type="password" wire:model="password_confirmation" class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">الصفة الوظيفية (الدور)</label>
            <select wire:model="role_id" class="mt-1 w-full p-3 border border-gray-300 rounded-lg bg-white">
                <option value="">-- اختر صفة --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->description ?? $role->name }}</option>
                @endforeach
            </select>
            @error('role_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition">
            تسجيل الحساب
        </button>
    </form>

    <div class="mt-6 text-center text-sm">
        <p class="text-gray-600">لديك حساب بالفعل؟ <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline">تسجيل الدخول</a></p>
    </div>
</div>
