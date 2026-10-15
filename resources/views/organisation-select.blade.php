<x-base-component tailwind="true" jQuery="true" subheading="Organisation Selection" metadata="Ujan Logistic - Agreement Vault" metadescription="Select the organisation you want to manage agreements for." bIcons="true">

    <div class="min-h-screen bg-[#030f1a] flex justify-center pt-10 px-4">

        <form method="POST" action="{{ route('select-organisation') }}" id="orga-select-form" class="w-full max-w-md">
            @csrf

            <label for="Orga_UIN" class="sr-only">Select an organisation</label>

            <div class="relative">
                <select
                    name="Orga_UIN"
                    id="Orga_UIN"
                    required
                    class="w-full appearance-none rounded-lg bg-[#101f30] border-2 border-[#3b82f6] text-slate-200
                           text-base font-medium text-center py-3 pl-4 pr-10
                           focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:border-[#3b82f6]
                           cursor-pointer transition-colors">
                    <option value="" disabled selected>-- Select Organization --</option>
                    @foreach ($companyInformation as $org)
                    <option value="{{ $org->Orga_UIN }}">{{ $org->Orga_Name }}</option>
                    @endforeach
                </select>

                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <noscript>
                <button type="submit" class="mt-3 w-full rounded-lg border border-slate-600 bg-[#101f30] text-slate-200 py-2">
                    Continue
                </button>
            </noscript>
        </form>

    </div>

    @section('extrajs')
    <script>
        $(function() {
            $('#Orga_UIN').on('change', function() {
                if ($(this).val()) {
                    $('#orga-select-form').trigger('submit');
                }
            });
        });
    </script>
    @endsection

</x-base-component>