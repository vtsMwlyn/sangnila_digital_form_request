<x-modal name="late-modal" maxWidth="2xl">
    <div class="p-6 text-gray-800">
        {{-- Tombol Close --}}
        <div class="flex justify-end">
            <button
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'late-modal' }))"
                class="text-red-500 hover:text-red-300 text-3xl font-bold"
            >
                &times;
            </button>
        </div>

        <p class="text-lg font-semibold mb-4 text-center">
            Select which balance will be reduced for total late this employee:
        </p>

        <div class="mb-6">
            <x-input-label for="totalLate" class="font-bold text-md mb-1">
                Total Late (in Day(s))
            </x-input-label>
            <x-text-input
                type="number"
                step="0.5"
                min="0"
                name="totalLate"
                id="totalLate"
                class="w-full border border-gray-400 rounded-lg px-3 py-2 text-lg focus:outline-none focus:ring-2 focus:ring-[#1EB8CD]"
            />
        </div>

        <div class="grid grid-cols-2 gap-5">
            <form method="post" action="{{ route('admin.late', ['mode' => 'leave']) }}">
                @csrf
                <input type="hidden" name="userId" id="lateUserIdLeave" />
                <input type="hidden" name="totalLateValue" id="totalLateLeave" />
                <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md transition duration-200 transform hover:scale-[1.02]">
                <i class="bi bi-calendar-check mr-2"></i> Leave Balance
                </button>
            </form>

            <form method="post" action="{{ route('admin.late', ['mode' => 'overwork']) }}">
                @csrf
                <input type="hidden" name="userId" id="lateUserIdOverwork" />
                <input type="hidden" name="totalLateValue" id="totalLateOverwork" />
                <button type="submit"
                class="w-full py-3 bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md transition duration-200 transform hover:scale-[1.02]">
                <i class="bi bi-alarm mr-2"></i> Overwork Balance
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const totalLateInput = document.getElementById('totalLate');
            const totalLateLeave = document.getElementById('totalLateLeave');
            const totalLateOverwork = document.getElementById('totalLateOverwork');

            totalLateInput.addEventListener('input', () => {
                totalLateLeave.value = totalLateInput.value;
                totalLateOverwork.value = totalLateInput.value;
            });
        });
    </script>
</x-modal>
