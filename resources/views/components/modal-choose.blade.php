@php
    use Illuminate\Support\Facades\Auth;
@endphp

<x-modal name="choose-modal" maxWidth="2xl">
    <div class="p-4">
        <div class="flex justify-end">
            <button
                type="button"
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'choose-modal' }))"
                class="text-red-500 hover:text-red-300 text-3xl font-bold"
            >
                &times;
            </button>
        </div>

        <div class="p-6 text-gray-800">
            <p class="text-lg font-semibold mb-4 text-center">
                Select which balance will be reduced for approving this employee's leave:
            </p>

            <div class="w-full grid grid-cols-2 gap-5">
                <form method="post" action="{{ route('admin.leave.approve', ['mode' => 'leave']) }}" class="w-full">
                    @csrf
                    <input type="hidden" name="leaveId" value="0" />
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md transition duration-200 transform hover:scale-[1.02]">
                        <i class="bi bi-calendar-check mr-2"></i>
                        Leave Balance
                    </button>
                </form>

                <form method="post" action="{{ route('admin.leave.approve', ['mode' => 'overwork']) }}" class="w-full">
                    @csrf
                    <input type="hidden" name="leaveId" value="0" />
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md transition duration-200 transform hover:scale-[1.02]">
                        <i class="bi bi-alarm mr-2"></i>
                        Overwork Balance
                    </button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // function openChooseModal(button) {
        //     const userId = button.getAttribute('data-user-id');
        //     const leaveId = button.getAttribute('data-leave-id');

        //     const form = document.getElementById('deductionForm');
        //     const actionUrl = `/deduction/update/${userId}/${leaveId}`;
        //     form.action = actionUrl;

        //     document.getElementById('leaveIdInput').value = leaveId;
        //     document.getElementById('userIdInput').value = userId;

        //     // Buka modal
        //     window.dispatchEvent(new CustomEvent('open-modal', { detail: 'choose-modal' }));
        // }

        // function setDeductionType(type) {
//     const form = document.getElementById('deductionForm');
//     const deductionInput = document.getElementById('deductionType');

//     deductionInput.value = type;

//     // 🚀 langsung ubah status jadi approved via form submit
//     form.submit();
// }
    </script>
</x-modal>
