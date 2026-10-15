<div class="card p-6">
    <h2 class="section-title">Additional Information</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- File No -->
        <div class="space-y-1.5">
            <label class="label" for="summary_file_no">File No</label>
            <input
                type="text"
                id="summary_file_no"
                class="input"
                name="file_no"
                value="{{ old('file_no', $fileNo) }}"
                @if($isDisabled) disabled @endif
                maxlength="4">

            @error('file_no')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Reference No -->
        <div class="space-y-1.5">
            <label class="label" for="summary_ref_no">Reference No.</label>
            <input
                type="text"
                id="summary_ref_no"
                class="input"
                name="ref_no"
                value="{{ old('ref_no', $refNo) }}"
                @if($isDisabled) disabled @endif>

            @error('ref_no')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Agreement Signed -->
        <div class="space-y-1.5">
            <label class="label" for="agreement_signed">Agreement Signed</label>

            <select
                id="agreement_signed"
                class="input"
                name="agreement_signed" @if($isDisabled) disabled @endif>
                <option class="bg-slate-800" value="" {{ old('agreement_signed', $agreementSigned) === null ? 'selected' : '' }}>Select an option</option>
                <option class="bg-slate-800" value="yes" {{ old('agreement_signed', $agreementSigned) === 'yes' ? 'selected' : '' }}>Yes</option>
                <option class="bg-slate-800" value="no" {{ old('agreement_signed', $agreementSigned) === 'no' ? 'selected' : '' }}>No</option>
            </select>

            @error('agreement_signed')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Signed Document Received Date -->
        <div class="space-y-1.5">
            <label class="label" for="signed_document_received_date">Signed Document Received Date</label>
            <input
                type="date"
                id="signed_document_received_date"
                class="input"
                name="signed_document_received_date"
                value="{{ old('signed_document_received_date', $signedDocumentReceivedDate) }}" @if($isDisabled) disabled @endif>

            @error('signed_document_received_date')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Agreement Received (copy type) -->
        <div class="space-y-1.5 col-span-2">
            <label class="label" for="agreement_received_type">Agreement Received</label>

            <select
                id="agreement_received_type"
                class="input"
                name="agreement_received_type" @if($isDisabled) disabled @endif>
                <option class="bg-slate-800" value="" {{ old('agreement_received_type', $agreementReceivedType) === null ? 'selected' : '' }}>Select an option</option>
                <option class="bg-slate-800" value="original_hard_copy" {{ old('agreement_received_type', $agreementReceivedType) === 'original_hard_copy' ? 'selected' : '' }}>Original Hard Copy</option>
                <option class="bg-slate-800" value="duplicate_hard_copy" {{ old('agreement_received_type', $agreementReceivedType) === 'duplicate_hard_copy' ? 'selected' : '' }}>Duplicate Hard Copy</option>
                <option class="bg-slate-800" value="pdf_copy" {{ old('agreement_received_type', $agreementReceivedType) === 'pdf_copy' ? 'selected' : '' }}>PDF Copy</option>
            </select>

            @error('agreement_received_type')
            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

    </div>
</div>