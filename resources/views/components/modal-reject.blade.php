<x-modal name="reject-modal" maxWidth="2xl">
    <div class="p-4">
        <div class="flex justify-end">
            <button
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'reject-modal' }))"
                class="text-red-500 hover:text-red-300 text-3xl font-bold"
            >
                <img src="{{ asset('img/close.svg') }}" alt="x" />
            </button>
        </div>

        <div class="p-5">
            <h2 class="text-2xl font-bold text-[#012967] mb-4 text-center">Reject Reason</h2>

            <form id="rejectForm" method="POST">
                @csrf
                <input type="hidden" name="rejected" id="rejectedValue">

                <label class="block text-sm font-medium text-[#012967] mb-2">
                    Please enter the rejection reason:
                </label>
                <x-textarea
                    name="admin_note"
                    id="adminNoteInput"
                    class="w-full"
                    placeholder="Enter reason here..."
                    required
                ></x-textarea>

                <div class="flex justify-end mt-5">
                    <button
                        type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded mr-2"
                        @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'reject-modal' }))"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="bg-[#1EB8CD] hover:bg-[#17A3B6] text-white font-semibold py-2 px-4 rounded"
                    >
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-modal>
