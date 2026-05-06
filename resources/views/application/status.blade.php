<x-layouts.app>
    <div class="max-w-xl mx-auto mt-10 p-8 bg-white rounded-xl shadow-lg text-center">

        @if($employee->status === 'pending')
            <div class="text-yellow-500 mb-4">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">طلبك قيد المراجعة</h2>
            <p class="text-gray-600 mt-2">يقوم فريق الموارد البشرية بتقييم سيرتك الذاتية. سنقوم بتحديث حالتك قريباً.</p>

        @elseif($employee->status === 'rejected')
            <div class="text-red-500 mb-4">
                <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">نعتذر، تم رفض طلبك</h2>
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800 text-right">
                <p class="font-bold">سبب الرفض:</p>
                <p class="mt-1">{{ $employee->rejection_reason ?? 'لم يتم تحديد سبب.' }}</p>
            </div>
            <p class="text-sm text-gray-500 mt-4">نتمنى لك التوفيق في مسيرتك المهنية.</p>
        @endif

        <div class="mt-8">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-blue-600 hover:underline">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</x-layouts.app>
