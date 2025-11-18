
<div class="sm:hidden">
    <select id="mobileFilter" class="w-[300px] border border-gray-300 rounded-md p-2 text-[#012967] font-semibold focus:ring-cyan-600 ">
        @auth
            @if (auth()->user()->role === 'user')
                <option value="all" {{ $type === 'all' ? 'selected' : '' }}>All Data</option>
                <option value="overwork" {{ $type === 'overwork' ? 'selected' : '' }}>Overwork</option>
                <option value="leave" {{ $type === 'leave' ? 'selected' : '' }}>Leave</option>
            @elseif (auth()->user()->role === 'admin')
                <option value="review" {{ $status === 'review' ? 'selected' : '' }}>Review</option>
                <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
            @endif
        @endauth
    </select>
</div>

<ul class="hidden sm:flex space-x-6 text-[#012967] font-semibold">
    @auth
        @if (auth()->user()->role === 'user')
            <input type="hidden" name="type" class="buttonSubmit" value="{{ $type }}">

            <li class="{{ $type === 'all' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="type" value="all"
                    class="status-btn hover:text-cyan-600 transition">All Data</button>
            </li>

            <li class="{{ $type === 'overwork' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="type" value="overwork"
                    class="status-btn hover:text-cyan-600 transition">Overwork</button>
            </li>

            <li class="{{ $type === 'leave' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="type" value="leave"
                    class="status-btn hover:text-cyan-600 transition">Leave</button>
            </li>

        @elseif (auth()->user()->role === 'admin')
            <input type="hidden" name="status" class="buttonSubmit" value="{{ $status }}">

            <li class="{{ $status === 'review' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="status" value="review"
                    class="status-btn hover:text-cyan-600 transition">Review</button>
            </li>

            <li class="{{ $status === 'approved' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="status" value="approved"
                    class="status-btn hover:text-cyan-600 transition">Approved</button>
            </li>

            <li class="{{ $status === 'rejected' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="status" value="rejected"
                    class="status-btn hover:text-cyan-600 transition">Rejected</button>
            </li>
        @endif
    @endauth
</ul>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.status-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const hiddenInput = document.querySelector('.buttonSubmit');
                hiddenInput.value = this.value;
                hiddenInput.form.submit();
            });
        });

        const mobileFilter = document.getElementById('mobileFilter');
        if (mobileFilter) {
            mobileFilter.addEventListener('change', function () {
                const hiddenInput = document.querySelector('.buttonSubmit');
                hiddenInput.value = this.value;
                hiddenInput.form.submit();
            });
        }

    });
</script>


{{-- <ul class="flex space-x-6 text-[#012967] font-semibold ">
    @auth
        @if (auth()->user()->role === 'user')
            <input type="hidden" name="type" class="buttonSubmit" value="{{ $type }}">
            <li class="{{ $type === 'all' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="type" value="all" class="status-btn hover:text-cyan-600 transition">All Data</button>
            </li>
            <li class="{{ $type === 'overwork' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="type" value="overwork" class="status-btn hover:text-cyan-600 transition">Overwork</button>
            </li>
            <li class="{{ $type === 'leave' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="type" value="leave" class="status-btn hover:text-cyan-600 transition">Leave</button>
            </li>
        @elseif (auth()->user()->role === 'admin')
            <input type="hidden" name="status" class="buttonSubmit" value="{{ $status }}">
            <li class="{{ $status === 'review' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="status" value="review" class="status-btn hover:text-cyan-600 transition">Review</button>
            </li>
            <li class="{{ $status === 'approved' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="status" value="approved" class="status-btn hover:text-cyan-600 transition">Approved</button>
            </li>
            <li class="{{ $status === 'rejected' ? 'border-b-4 border-cyan-400 pb-1' : '' }} cursor-pointer">
                <button type="button" name="status" value="rejected" class="status-btn hover:text-cyan-600 transition">Rejected</button>
            </li>
        @endif
    @endauth
</ul> --}}
