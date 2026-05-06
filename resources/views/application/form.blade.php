<x-layouts.app>
    <div class="max-w-2xl mx-auto mt-10 p-8 bg-white rounded-xl shadow-lg" x-data="{ isUploading: false }">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">تقديم طلب توظيف</h2>

        <form action="{{ route('job.store') }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700">المسمى الوظيفي المتقدم له</label>
                    <input type="text" name="job_title" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">الراتب المتوقع ($)</label>
                    <input type="number" name="expected_salary" min="0" required class="mt-1 w-full p-3 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700">السيرة الذاتية (PDF, Word)</label>
                    <input type="file" name="resume_file" accept=".pdf,.doc,.docx" required class="mt-1 w-full p-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>

                <button type="submit" x-bind:disabled="isUploading" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 transition flex justify-center items-center">
                    <span x-show="!isUploading">إرسال الطلب</span>
                    <span x-show="isUploading" class="animate-pulse">جاري الرفع...</span>
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>
