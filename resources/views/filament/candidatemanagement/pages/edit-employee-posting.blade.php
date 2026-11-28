<x-filament-panels::page>
    <style>
        .ep-wrap {
            max-width: 64rem;
            margin: 0 auto;
            width: 100%;
            padding: 2rem 1.5rem;
        }

        .ep-muted {
            color: #94a3b8;
        }

        .figma-card {
            background-color: rgb(30 41 59 / .4);
            border: 1px solid #334155;
            border-radius: 1rem;
            overflow: hidden;
        }

        .figma-card-header {
            color: #bbf7d0;
            font-size: 1.125rem;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #334155;
        }

        .form-input-figma,
        .form-select-figma {
            width: 100%;
            margin-top: .25rem;
            background-color: rgb(15 23 42 / .7);
            border: 1px solid #475569;
            border-radius: .375rem;
            color: #fff;
            color-scheme: dark;
            font-size: .875rem;
            padding: .55rem .75rem;
        }

        .form-input-figma:focus,
        .form-select-figma:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 1px #3b82f6;
        }

        .form-select-figma option {
            background-color: #0f172a;
        }

        .ep-invalid {
            border-color: #ef4444;
        }

        .figma-button-primary {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background-color: #2563eb;
            color: #fff;
            font-size: .875rem;
            font-weight: 600;
            border-radius: .375rem;
            padding: .6rem 1.25rem;
        }

        .figma-button-primary:hover {
            background-color: #1d4ed8;
        }

        .figma-button-primary:disabled {
            opacity: .5;
        }
    </style>

    <x-secondary-header
        :backurl="\App\Filament\Candidatemanagement\Pages\EmployeePosting::getUrl()"
        :companyInformation="$this->selectedCompany"
        :userData="$this->userData"
        title="Edit Posting"
        backurlforSettings="https://partakers.in/dashboard" />

    <div class="ep-wrap">
        <x-globalresponse />

        {{-- Employee --}}
        <div class="mb-6">
            <h1 class="text-xl font-semibold">{{ $employeeName }}</h1>
            @if ($updatedOn)
            <p class="ep-muted mt-1 text-sm">
                Last updated:
                {{ \Carbon\Carbon::parse($updatedOn)->timezone('Asia/Kolkata')->format('d M Y, h:i A') }} IST
            </p>
            @endif
        </div>

        <form wire:submit="save" class="grid grid-cols-1 gap-8">
            {{-- Error Summary --}}
            @if ($errors->any())
            <div class="rounded-md border border-red-500/50 bg-red-500/10 p-4">
                <div class="mb-2 flex items-center gap-2 text-sm font-semibold text-red-400">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Please correct the issues below.
                </div>
                @foreach ($errors->all() as $error)
                <p class="text-xs text-red-300">{{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- Posting Details --}}
            <div class="figma-card">
                <h2 class="figma-card-header"><i class="bi bi-briefcase-fill mr-2"></i>Posting Details</h2>
                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                    @foreach ($this->fields as $column => $field)
                    <div wire:key="field-{{ $column }}">
                        <label for="{{ $column }}" class="text-sm font-medium">{{ $field['label'] }}</label>
                        <select id="{{ $column }}" wire:model="form.{{ $column }}"
                            @class(['form-select-figma', 'ep-invalid'=> $errors->has("form.{$column}")])>
                            <option value="">Select...</option>
                            @foreach ($field['options'] as $value => $name)
                            <option value="{{ $value }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error("form.{$column}")
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Duration --}}
            <div class="figma-card">
                <h2 class="figma-card-header"><i class="bi bi-calendar3 mr-2"></i>Duration</h2>
                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                    @foreach (['Strt_Dt' => 'Start Date', 'End_Dt' => 'End Date'] as $column => $label)
                    <div>
                        <label for="{{ $column }}" class="text-sm font-medium">{{ $label }}</label>
                        <input type="date" id="{{ $column }}" wire:model="form.{{ $column }}"
                            @class(['form-input-figma', 'ep-invalid'=> $errors->has("form.{$column}")])>
                        @error("form.{$column}")
                        <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end">
                <button type="submit" class="figma-button-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save"><i class="bi bi-check2-circle"></i> Update</span>
                    <span wire:loading wire:target="save">Updating...</span>
                </button>
            </div>
        </form>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js"></script>
    <script src="{{ asset('assets/header-functionality.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
</x-filament-panels::page>