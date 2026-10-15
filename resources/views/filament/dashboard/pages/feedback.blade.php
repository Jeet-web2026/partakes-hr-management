<x-filament-panels::page>
    <style>
        .input,
        select.input,
        textarea.input {
            width: 100%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: .5rem;
            padding: .5rem .75rem;
            color: #e2e8f0;
            font-size: .875rem;
        }

        .input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .15);
        }
    </style>
    <div class="min-h-screen py-6 w-full rounded text-white">

        <x-globalresponse />

        <form action="{{ route('feedback-store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="orga_uin" value="{{ $orgaUin }}">

            <div class="bg-card border border-white/10 rounded-lg p-8">
                <h1 class="text-center text-xl font-medium text-white mb-8">
                    Page Experience Review
                </h1>
                <div class="mb-6">
                    <label class="block text-white mb-2">Which page are you reviewing?</label>
                    <select
                        name="page_uin"
                        class="w-full rounded-md input px-3 py-2 text-white">
                        <option value="" class="bg-slate-900" disabled {{ old('page_uin') ? '' : 'selected' }}>Select a page name</option>
                        @forelse($pages as $page)                        
                            <option value="{{ $page->Page_UIN }}" class="bg-slate-900 text-white" @selected(old('page_uin')===$page->Page_UIN )>{{ $page->Page_name }}</option>
                        @empty
                            <option value="" class="bg-slate-900" disabled>No pages available</option>
                        @endforelse
                    </select>
                    @error('page_uin') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                </div>

                <p class="text-white mb-3 text-white">How satisfied were you using this page?</p>

                <div class="lg:overflow-hidden rounded-md border border-white/10 overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr>
                                <th class="bg-card border border-white/10 px-4 py-3 font-semibold text-white">
                                    Page Rating
                                </th>
                                <th class="bg-green-700 border border-white/10 px-4 py-3 font-semibold text-white text-center">
                                    Very Good
                                </th>
                                <th class="bg-lime-600 border border-white/10 px-4 py-3 font-semibold text-white text-center">
                                    Good
                                </th>
                                <th class="bg-yellow-500 border border-white/10 px-4 py-3 font-semibold text-white text-center">
                                    Ok
                                </th>
                                <th class="bg-amber-700 border border-white/10 px-4 py-3 font-semibold text-white text-center">
                                    Poor
                                </th>
                                <th class="bg-red-700 border border-white/10 px-4 py-3 font-semibold text-white text-center">
                                    Very Poor
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $rows = [
                            'page_load_speed' => 'Page Load Speed',
                            'page_design' => 'Page Design',
                            'ease_of_use' => 'Ease of Use',
                            'information_clarity' => 'Information Clarity',
                            'overall_experience' => 'Overall Experience',
                            ];
                            $columns = ['very_good', 'good', 'ok', 'poor', 'very_poor'];
                            @endphp

                            @foreach ($rows as $rowKey => $rowLabel)
                            <tr>
                                <td class="bg-card border border-white/10 px-4 py-3 text-white">
                                    {{ $rowLabel }}
                                </td>
                                @foreach ($columns as $col)
                                <td class="bg-card border border-white/10 px-4 py-3 text-center">
                                    <input
                                        type="radio"
                                        name="{{ $rowKey }}"
                                        value="{{ $col }}"
                                        @checked(old($rowKey)===$col)
                                        class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer" />
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @error('page_load_speed') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                @error('page_design') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                @error('ease_of_use') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                @error('information_clarity') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                @error('overall_experience') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror

                <p class="text-sm text-white mt-2">Tick mark one of the cells</p>
            </div>

            <div class="bg-card border border-white/10 rounded-lg p-8">
                <h1 class="text-center text-xl font-medium text-white mb-8">
                    Share Your Feedback
                </h1>

                <div class="flex flex-col gap-4 mt-6 lg:flex-row items-center">
                    <div class="flex-1 w-full">
                        <label class="block text-white mb-2">Page Feedback</label>
                        <select
                            name="feedback_type"
                            class="w-full rounded-md input px-3 py-2 text-white">
                            <option value="" class="bg-slate-900" disabled {{ old('feedback_type') ? '' : 'selected' }}>Select a type</option>
                            <option value="Query/Question" class="bg-slate-900" @selected(old('feedback_type')==='Query/Question' )>Query/Question</option>
                            <option value="Page Bug/Error" class="bg-slate-900" @selected(old('feedback_type')==='Page Bug/Error' )>Page Bug/Error</option>
                            <option value="Incorrect Information" class="bg-slate-900" @selected(old('feedback_type')==='Incorrect Information' )>Incorrect Information</option>
                            <option value="Compliment / Positive Feedback" class="bg-slate-900" @selected(old('feedback_type')==='Compliment / Positive Feedback' )>Compliment / Positive Feedback</option>
                            <option value=" Suggestion for Improvement" class="bg-slate-900" @selected(old('feedback_type')===' Suggestion for Improvement' )> Suggestion for Improvement</option>
                            <option value="Idea/New Features" class="bg-slate-900" @selected(old('feedback_type')==='Idea/New Features' )>Idea/New Features</option>
                            <option value="General Feedback" class="bg-slate-900" @selected(old('feedback_type')==='General Feedback' )>General Feedback</option>
                            <option value="Other" class="bg-slate-900" @selected(old('feedback_type')==='Other' )>Other</option>
                        </select>
                        @error('feedback_type') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex-1 w-full">
                        <div class="flex gap-2" x-data="{
                                open: false,
                                selected: {{ old('country_uin', 101107) }},
                                countries: {{ $countryDetails->toJson() }},
                                get selectedCountry() {
                                    return this.countries.find(c => c.Admn_Cutr_Mast_UIN == this.selected) ?? null;
                                }
                            }">

                            <div class="relative">
                                <label class="block text-white mb-2">Country</label>
                                <input type="hidden" name="country_uin" :value="selected">

                                <button
                                    type="button"
                                    @click="open = !open"
                                    @click.outside="open = false"
                                    class="w-full flex items-center justify-between gap-2 rounded-md input px-3 py-2 text-white bg-slate-900 border border-white/10 border-r-0 rounded-r-none">

                                    <span class="flex items-center gap-2" x-show="selectedCountry">
                                        <img :src="`https://flagsapi.com/${selectedCountry?.Code?.trim()}/flat/24.png`" class="w-full h-full object-cover"  loading="lazy">
                                        <span x-text="selectedCountry?.Name"></span>
                                    </span>

                                    <span x-show="!selectedCountry" class="text-slate-400">Select Country</span>

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <ul
                                    x-show="open"
                                    x-transition
                                    x-cloak
                                    class="absolute z-10 mt-1 w-70 h-70 overflow-y-auto rounded-md bg-slate-900 border border-white/10 shadow-lg">

                                    <template x-for="country in countries" :key="country.Admn_Cutr_Mast_UIN">
                                        <li
                                            @click="selected = country.Admn_Cutr_Mast_UIN; open = false"
                                            class="flex items-center gap-2 px-3 py-2 text-white cursor-pointer"
                                            :class="selected == country.Admn_Cutr_Mast_UIN && 'bg-slate-800'">
                                            <span class="fi w-5 h-4 inline-block flex-shrink-0 rounded-sm overflow-hidden bg-cover bg-center bg-no-repeat">
                                                <img :src="`https://flagsapi.com/${country.Code.trim()}/flat/64.png`" class="w-full h-full object-cover"  loading="lazy">
                                            </span>
                                            <span x-text="country.Name"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        <div class="flex-1">
                            <label class="block text-white mb-2">Present Location Pincode</label>
                            <input
                                type="text"
                                name="pincode"
                                value="{{ old('pincode') }}"
                                placeholder="Enter your pin code"
                                class="w-full rounded-md input pe-3 py-3 text-white placeholder-slate-400 border border-white/10" />
                            </div>
                        </div>
                        @error('pincode') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-white mb-2">Tell Us More</label>
                    <textarea
                        name="comments"
                        rows="4"
                        placeholder="Comments here"
                        class="w-full rounded-md input px-3 py-2 text-white placeholder-slate-400 border border-white/10">{{ old('comments') }}</textarea>
                    @error('comments') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-col gap-4 mt-6 lg:flex-row items-center">
                    <div class="flex-1 w-full">
                        <label class="block text-white mb-4">
                            Did you find what you were looking for?
                        </label>

                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                <input
                                    type="radio"
                                    name="Lokn_for"
                                    value="Yes"
                                    class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    @checked(old('Lokn_for')==='Yes' )>
                                <span>Yes</span>
                            </label>

                            <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                <input
                                    type="radio"
                                    name="Lokn_for"
                                    value="No"
                                    class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    @checked(old('Lokn_for')==='No' )>
                                <span>No</span>
                            </label>
                        </div>

                        @error('Lokn_for')
                        <p class="text-sm text-red-700 mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- Would you recommend this page to others? -->
                    <div class="flex-1 w-full">
                        <label class="block text-white mb-4">
                            Would you recommend this page to others?
                        </label>

                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                <input
                                    type="radio"
                                    name="Rcmd_Page"
                                    value="Yes"
                                    class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    @checked(old('Rcmd_Page')==='Yes' )>
                                <span>Yes</span>
                            </label>

                            <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                <input
                                    type="radio"
                                    name="Rcmd_Page"
                                    value="No"
                                    class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    @checked(old('Rcmd_Page')==='No' )>
                                <span>No</span>
                            </label>
                        </div>

                        @error('Rcmd_Page')
                        <p class="text-sm text-red-700 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-end justify-between flex-col lg:flex-row gap-4">
                    <div class="flex-1 w-full">
                        <label class="block text-white mb-4">
                            Would You Like Us to Contact You?
                        </label>

                        <div class="flex flex-wrap gap-4">
                            <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                <input
                                    type="radio"
                                    name="Cnta_Us"
                                    value="Yes"
                                    class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    @checked(old('Cnta_Us')==='Yes' )>
                                <span>Yes</span>
                            </label>

                            <label class="flex items-center gap-2 text-white text-sm cursor-pointer">
                                <input
                                    type="radio"
                                    name="Cnta_Us"
                                    value="No"
                                    class="h-5 w-5 rounded border-2 border-slate-400 text-blue-600 focus:ring-blue-500 cursor-pointer"
                                    @checked(old('Cnta_Us')==='No' )>
                                <span>No</span>
                            </label>
                        </div>

                        @error('Cnta_Us')
                        <p class="text-sm text-red-700 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-white mb-2">Attachment</label>
                        <input
                            type="file"
                            class="input file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-slate-700 file:text-slate-100 file:text-sm hover:file:bg-slate-600 cursor-pointer"
                            name="attachment"
                            id="attachment"
                            accept=".png,.pdf,.jpg,.jpeg">
                        @error('attachment') <p class="text-sm text-red-700 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button
                    type="submit"
                    class="bg-blue-800 mt-5 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-md transition-colors">
                    Submit
                </button>
            </div>

        </form>

    </div>

    <script>
        var AGREEMENTS_URL = "{{ route('drafts.data') }}";
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/4.0.0/jquery.min.js" integrity="sha512-8LENNbXmzI/Gbj+OwXmqR6V4QaUAw0/porPzy1+dQoJqC0JPHedWoe0DDOTL2uHA5XXJyIsPtiMHH86pVlay6A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/feedback-page-functions.js') }}"></script>
    <script src="{{ asset('assets/common.js') }}"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</x-filament-panels::page>