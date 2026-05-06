<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\EmployeeResource\Pages;
use App\Filament\Hr\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('user_id')
    ->label('حساب المستخدم')
    ->relationship(
        name: 'user',
        titleAttribute: 'name',
        modifyQueryUsing: function (Builder $query, ?Employee $record) {
            $query->whereHas('role', function ($q) {
                // 1. جلب الحسابات التي دورها "موظف" فقط
                $q->where('name', 'employee');
            })
            ->where(function ($q) use ($record) {
                // 2. جلب الحسابات التي لم يتم تعيينها بعد (ليس لها ملف موظف)
                $q->doesntHave('employee');

                // 3. (حماية لتجنب الأخطاء): إذا كنا في صفحة "التعديل"،
                // يجب أن نسمح بعرض اسم المستخدم المربوط حالياً بهذا الموظف
                if ($record) {
                    $q->orWhere('id', $record->user_id);
                }
            });
        }
    )
    ->required()
    ->unique(ignoreRecord: true)
    ->searchable()

    // (اختياري) أضفت لك زر إنشاء مستخدم سريع من الرد السابق لتبقى لوحتك متكاملة
    ->createOptionForm([
        Forms\Components\TextInput::make('name')->label('الاسم الكامل')->required(),
        Forms\Components\TextInput::make('email')->label('البريد الإلكتروني')->email()->required()->unique('users', 'email'),
        Forms\Components\TextInput::make('password')->label('كلمة المرور')->password()->required(),
    ])
    ->createOptionUsing(function (array $data) {
        $role = \App\Models\Role::where('name', 'employee')->first();
        $user = \App\Models\User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role_id' => $role ? $role->id : null,
        ]);
        return $user->id;
    }),

                    Forms\Components\TextInput::make('job_title')
                        ->label('المسمى الوظيفي')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('salary')
                        ->label('الراتب')
                        ->numeric()
                        ->prefix('$'),
                    Forms\Components\Select::make('status')
                        ->label('حالة الموظف')
                        ->options([
                            'active' => 'على رأس العمل',
                            'on_leave' => 'في إجازة',
                            'terminated' => 'مفصول',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\DatePicker::make('hire_date')
                        ->label('تاريخ التعيين'),
                ])->columns(2);
}


    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.name')
                ->label('اسم الموظف')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('job_title')
                ->label('المسمى الوظيفي')
                ->searchable(),
            Tables\Columns\TextColumn::make('salary')
                ->label('الراتب')
                ->money('usd')
                ->sortable(),
Tables\Columns\TextColumn::make('status')
    ->label('الحالة')
    ->badge()
    // ترجمة الكلمة الإنجليزية إلى عربية في الواجهة
    ->formatStateUsing(fn (string $state): string => match ($state) {
        'pending' => 'قيد المراجعة',
        'active' => 'على رأس العمل',
        'on_leave' => 'في إجازة',
        'terminated' => 'مفصول',
        'rejected' => 'مرفوض طلب التوظيف',
        default => $state,
    })
    // إعطاء لون لكل حالة مع وضع default لتجنب هذا الخطأ مستقبلاً
    ->color(fn (string $state): string => match ($state) {
        'pending' => 'warning',
        'active' => 'success',
        'on_leave' => 'info',
        'terminated' => 'danger',
        'rejected' => 'danger',
        default => 'gray',
    }),
                Tables\Columns\TextColumn::make('hire_date')
                ->label('تاريخ التعيين')
                ->date()
                ->sortable(),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->label('تصفية حسب الحالة')
                ->options([
                    'active' => 'على رأس العمل',
                    'on_leave' => 'في إجازة',
                    'terminated' => 'مفصول'
                ])
            ])
->actions([
    Tables\Actions\EditAction::make(),

    // زر القبول (يظهر فقط لمن حالتهم pending)
    Tables\Actions\Action::make('accept')
        ->label('قبول وتوظيف')
        ->color('success')
        ->icon('heroicon-o-check-circle')
        ->requiresConfirmation()
        ->action(function (Employee $record) {
            $record->update([
                'status' => 'active',
                'hire_date' => now(), // تسجيل تاريخ اليوم كتاريخ تعيين
            ]);
        })
        ->visible(fn (Employee $record) => $record->status === 'pending'),

    // زر الرفض (يفتح نافذة لطلب سبب الرفض)
    Tables\Actions\Action::make('reject')
        ->label('رفض الطلب')
        ->color('danger')
        ->icon('heroicon-o-x-circle')
        ->form([
            Forms\Components\Textarea::make('rejection_reason')
                ->label('سبب الرفض (سيتم عرضه للمتقدم)')
                ->required()
        ])
        ->action(function (Employee $record, array $data) {
            $record->update([
                'status' => 'rejected',
                'rejection_reason' => $data['rejection_reason'],
            ]);
        })
        ->visible(fn (Employee $record) => $record->status === 'pending'),
])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [
                    RelationManagers\SkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
