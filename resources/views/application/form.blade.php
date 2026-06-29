<x-layouts.app>
    <div x-data="{ isUploading: false }">
        @if(session('success'))
            <div class="success-alert">
                {{ session('success') }}
            </div>
        @endif

        <div style="text-align: center; margin-bottom: 2rem;">
            <h2>{{ __('filament.auth.job_application') }}</h2>
            <p class="subtitle">{{ __('filament.auth.join_team') }}</p>
        </div>

        @if($vacancies->isEmpty())
            <div class="success-alert" style="background: rgba(234,179,8,0.1); border-color: rgba(234,179,8,0.3);">
                لا توجد وظائف شاغرة حالياً. يرجى المحاولة لاحقاً.
            </div>
        @else
        <form action="{{ route('job.store') }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true">
            @csrf
            <div class="form-group">
                <label>{{ __('filament.auth.select_vacancy') }}</label>
                <select name="vacancy_id" required>
                    <option value="" disabled selected>{{ __('filament.auth.choose_vacancy') }}</option>
                    @foreach($vacancies as $vacancy)
                        <option value="{{ $vacancy->id }}">{{ $vacancy->title }} @if($vacancy->location) - {{ $vacancy->location }} @endif</option>
                    @endforeach
                </select>
                @error('vacancy_id') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>{{ __('filament.auth.expected_salary') }}</label>
                <input type="number" name="expected_salary" min="0" required placeholder="0" value="{{ old('expected_salary') }}">
                @error('expected_salary') <span class="error-text">{{ $message }}</span> @enderror
            </div>



            <div class="form-group">
                <label>{{ __('filament.auth.resume_file') }}</label>
                <input type="file" name="resume_file" accept=".pdf,.doc,.docx" required>
                @error('resume_file') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <button type="submit" x-bind:disabled="isUploading" class="btn-primary">
                <span x-show="!isUploading">{{ __('filament.auth.submit_application') }}</span>
                <span x-show="isUploading" x-cloak>{{ __('filament.auth.uploading') }}</span>
            </button>
        </form>
        @endif
    </div>
</x-layouts.app>
