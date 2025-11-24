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
                <img src="{{ asset('img/close.svg') }}" alt="x" />
            </button>
        </div>

        <div class="p-6 text-gray-800">
            <p class="text-lg font-semibold mb-4 text-center">
                Select which balance will be reduced for approving this employee's leave:
            </p>

            <p class="font-semibold text-cyan-500 mb-4 text-center">Requested: <span id="choose-modal-requested"></span></p>

            <div class="w-full grid grid-cols-2 gap-5">
                <form method="post" action="{{ route('admin.leave.approve', ['mode' => 'leave']) }}" class="w-full flex flex-col items-center">
                    @csrf
                    <input type="hidden" name="leaveId" value="0" />
                    <p class="mb-2 text-slate-500" id="choose-modal-user-leave-balance"></p>
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md transition duration-200 transform hover:scale-[1.02]">
                        <i class="bi bi-calendar-check mr-2"></i>
                        Leave Balance
                    </button>
                </form>

                <form method="post" action="{{ route('admin.leave.approve', ['mode' => 'overwork']) }}" class="w-full flex flex-col items-center">
                    @csrf
                    <input type="hidden" name="leaveId" value="0" />
                    <p class="mb-2 text-slate-500" id="choose-modal-user-overwork-balance"></p>
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-700 text-white font-semibold rounded-xl shadow-md transition duration-200 transform hover:scale-[1.02]">
                        <i class="bi bi-alarm mr-2"></i>
                        Overwork Balance
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-modal>
