<?php

namespace App\Filament\Settings\Pages;

use App\Filament\Candidatemanagement\Pages\Common\HasCompanyHeader;
use App\Services\ApplicationManagement\InterviewStepService;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Throwable;

class InterviewStages extends Page
{
    use HasCompanyHeader;

    private const NAME_COLUMN = 'Stage_Name';
    private const ACTIVE_COLUMN = 'Stage_Status';

    protected static ?string $navigationLabel = 'Interview Stages';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?int $navigationSort = 11;
    protected static ?string $slug = 'interview-stages';

    protected string $view = 'filament.settings.pages.interview-stages';

    #[Locked]
    public string $userUin = '';

    #[Locked]
    public string $orgaUin = '';

    #[Locked]
    public ?string $editingId = null;

    public string $search = '';
    public string $editName = '';
    public string $editStatus = 'active';

    public function mount(): void
    {
        $this->userUin = (string) Session::get('partaker_user_uin');
        $this->orgaUin = (string) Session::get('selected_orga_uin');

        if (! $this->userUin || ! $this->orgaUin) {
            $this->redirect(route('home'));
        }
    }

    #[Computed]
    public function stages(): Collection
    {
        $stages = app(InterviewStepService::class)->listForOrganisation($this->orgaUin);
        $term = mb_strtolower(trim($this->search));

        if ($term === '') {
            return $stages;
        }

        return $stages
            ->filter(fn($stage) => str_contains(
                mb_strtolower((string) $stage->{self::NAME_COLUMN}),
                $term
            ))
            ->values();
    }

    public function openEdit(string $id): void
    {
        $stage = $this->findStage($id);

        $this->editingId = (string) $stage->getKey();
        $this->editName = (string) $stage->{self::NAME_COLUMN};
        $this->editStatus = $stage->{self::ACTIVE_COLUMN} ? 'active' : 'inactive';
        $this->resetErrorBag();
    }

    public function closeEdit(): void
    {
        $this->reset(['editingId', 'editName']);
        $this->editStatus = 'active';
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $this->editName = trim($this->editName);

        $this->validate([
            'editName' => ['required', 'string', 'max:100'],
            'editStatus' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        $stage = $this->findStage((string) $this->editingId);

        try {
            app(InterviewStepService::class)->updateStep($stage, [
                self::NAME_COLUMN => $this->editName,
                self::ACTIVE_COLUMN => $this->editStatus === 'active',
            ], $this->userUin);
        } catch (ValidationException $e) {
            $this->addError('editName', $e->validator->errors()->first());
            return;
        } catch (Throwable $e) {
            report($e);
            session()->now('error', 'Could not update the interview stage. Please try again.');
            return;
        }

        unset($this->stages);
        $this->closeEdit();

        session()->now('success', 'Interview stage updated successfully.');
    }

    protected function findStage(string $id)
    {
        $stage = app(InterviewStepService::class)
            ->listForOrganisation($this->orgaUin)
            ->first(fn($item) => (string) $item->getKey() === $id);

        abort_unless($stage, 404);

        return $stage;
    }
}
