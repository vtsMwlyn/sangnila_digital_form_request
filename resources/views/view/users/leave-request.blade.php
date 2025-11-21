@extends('layouts.app')

@section('content')
    <form
        action="{{ isset($leave) ? route('leave.update', $leave) : route('leave.insert') }}"
        method="post"
        enctype="multipart/form-data"
        class="container-draft bg-[#FEFEFEB2] p-8 rounded-2xl w-full shadow-lg overflow-x-auto"
    >
        @csrf @if(isset($leave)) @method('PUT') @endif

        <x-back-button onclick="history.back();" />

        <h2 class="text-[#042E66] text-3xl font-black mt-2 mb-1">
            New Leave Request
        </h2>
        <span class="text-slate-500 italic">Pengajuan Cuti</span>
        <x-separator-line/>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-10 mt-5">
            {{-- Submission Section --}}
            <div class="flex-1">
                <h3 class="text-blue-800 font-extrabold text-lg mb-4">
                    Employee Information
                </h3>
                <x-submisson
                    :allowance="$allowance"
                    :leave_period="$leave_period"
                />
            </div>

            {{-- Leave Request Section --}}
            <div class="flex-1 flex flex-col xl:space-y-4 ">
                <h3 class="text-blue-800 font-extrabold text-lg xl:mt-0 mt-2">Leave Information</h3>

                {{-- Start Date --}}
                <div class="flex flex-col w-full">
                    <div class="rangeTime flex flex-col w-full">
                        <x-input-label
                            for="startDate"
                            class="font-bold text-md mb-1"
                        >
                            Start Date: <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input
                            type="date"
                            name="start_leave"
                            id="startDate"
                            value="{{ old('start_leave', isset($leave) ? $leave->start_leave : '') }}"
                            class="w-full"
                        />
                        <x-unvalid-input field="start_leave" />
                    </div>
                </div>

                {{-- How Many Days --}}
                <div class="flex flex-col mt-3">
                    <div class="rangeTime flex w-full">
                        <x-input-label
                            for="manyDays"
                            class="font-bold text-md mb-1 w-[280px] xl:w-full"
                        >
                            How Many Days? <span class="text-red-500">*</span>
                        </x-input-label>
                    </div>

                    <div class="flex flex-row h-10">
                        @php $num = 0; $numeric = 0; @endphp @if (isset($leave))
                        @php $leave_period = $leave->leave_period; $real =
                        $leave_period / 8; $num = floor($real); $dec = $real -
                        floor($real); if ($dec < .5) { $numeric = $num;
                        $labelNumeric = $numeric . ' Day(s)'; $decimal = $dec *
                        8; $labelDecimal = $decimal . ' Hours(s)'; } elseif
                        ($dec === 0.5) { $numeric = $real; $labelNumeric =
                        $numeric . ' Day(s)'; $decimal = 0; $labelDecimal =
                        $decimal . ' Hour(s)'; } elseif ($dec >= 0.5) { $numeric
                        = $real - ($dec - .5); $labelNumeric = floor($numeric)
                        == 0 ? ($numeric - floor($numeric)) * 8 . ' Hour(s)' :
                        floor($numeric) . ' Day(s) ' . ($numeric -
                        floor($numeric)) * 8 . ' Hour(s)'; $decimal = ($dec -
                        .5) * 8; $labelDecimal = $decimal . ' hour(s)'; }
                        @endphp @endif

                        <x-text-input
                            type="number"
                            step="0.5"
                            min="0"
                            oninput="if(this.value < 0) this.value = 0;"
                            onblur="if(this.value === '') this.value = 0;"
                            name="many_days"
                            id="manyDays"
                            value="{{ old('many_days', isset($leave) ? $numeric : '0') }}"
                            class="w-[280px] xl:w-1/2"
                        />
                        <span id="daysLabel" class="text-gray-500 mt-2 ml-2 text-xs xl:text-lg">
                            @if (isset($leave)) {{ old('many_days',
                            $labelNumeric) }} @endif
                        </span>
                    </div>

                    <x-unvalid-input field="many_days" />
                </div>

                {{-- How Many Hours --}}
                <div class="flex flex-col mt-3">
                    <div class="rangeTime flex w-full">
                        <x-input-label
                            for="manyHour"
                            class="font-bold text-md mb-1"
                        >
                            How Many Hours? <span class="text-red-500">*</span>
                        </x-input-label>
                    </div>

                    <div class="flex flex-row h-10">
                        <x-text-input
                            type="number"
                            min="0"
                            step="0.5"
                            oninput="if(this.value < 0) this.value = 0;"
                            onblur="if(this.value === '') this.value = 0;"
                            name="many_hours"
                            id="manyHours"
                            value="{{ old('many_hours', isset($leave) ? $decimal : '0') }}"
                            class="w-[280px] xl:w-1/2"
                        />
                        <span id="hoursLabel" class="text-gray-500 mt-2 ml-2 text-xs xl:text-lg">
                            @if (isset($leave)) {{ old('many_hours',
                            $labelDecimal) }} @endif
                        </span>
                    </div>

                    <x-unvalid-input field="many_hours" />
                </div>

                {{-- Reason --}}
                <div>
                    <x-input-label for="reason" class="font-bold text-md mb-1">
                        Leave Reason: <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-textarea
                        name="reason"
                        id="reason"
                        rows="4"
                        placeholder="Write your leave reason here..."
                        class="w-full"
                    >
                        {{ old('reason', isset($leave) ? $leave->reason : '') }}
                    </x-textarea>
                    <x-unvalid-input field="reason" />
                </div>

                {{-- Buttons --}}
                <div class="w-full flex justify-end mt-4">
                    <div class="w-full xl:w-2/3 gap-2 grid grid-cols-2 mt-6">
                        <button
                            type="submit"
                            name="action"
                            value="draft"
                            class="flex items-center justify-center hover:scale-105 bg-slate-500 rounded-xl px-4 py-2 text-sm text-white hover:brightness-125 transition"
                        >
                            <i class="bi bi-save2 mt-1 mr-1 h-[24px] w-[24px]"></i>
                            Draft
                        </button>

                        <x-button
                            type="submit"
                            name="action"
                            value="submit"
                            class="w-full"
                        >
                            <i class="bi bi-send-fill mt-1 mr-1 h-[24px] w-[24px]"></i>
                            Submit
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
          const daysLabel = document.getElementById('daysLabel');
          const hoursLabel = document.getElementById('hoursLabel');
          const manyDays = document.getElementById('manyDays');
          const manyHours = document.getElementById('manyHours');
          const hasLeave = {{ isset($leave) ? 'true' : 'false' }};

          fetch('/leave_allowance', {
            headers: {
                'Accept': 'application/json'
            }
          })
            .then(response => response.json())
            .then(data => {
              let allowance = {{ max(Auth::user()->overwork_allowance, Auth::user()->total_overwork) }};
              console.log('Jatah lembur/cuti paling banyak: ', allowance)
              const res = allowance / 8;
              console.log('Hasil:', res);

              manyHours.addEventListener('input', () => {
                hoursLabel.textContent = `${manyHours.value} Hour(s)`;
              });

              manyDays.max = Math.floor(res);
              console.log('Max day: ', manyDays.max)
              manyHours.max = (res - Math.floor(res)) * 8;
              console.log('Max hour: ', manyHours.max)

                if (Math.floor(res) === {{ $num }} && hasLeave === false) {
                manyHours.max = (res - Math.floor(res)) * 8;
                } else if ({{ $numeric }} - Math.floor({{ $numeric }}) > 0) {
                manyHours.max = ({{ $numeric }} - Math.floor({{ $numeric }})) * 8 - 1;
                } else {
                manyHours.max = 7;
                }

              manyDays.addEventListener('input', function () {
                let dayValue = this.value;
                let numericDecimal = parseFloat(dayValue);
                let integer = Math.floor(dayValue);
                let decimal = dayValue - Math.floor(dayValue);

                if (decimal === 0 && numericDecimal > 1) {
                  daysLabel.textContent = `${Math.floor(dayValue)} Day(s)`;
                } else if (decimal == .5 && numericDecimal > 1) {
                  daysLabel.textContent = `${Math.floor(dayValue)} Day(s) ${decimal * 8} Hour(s)`;
                } else if (decimal === 0 && numericDecimal <= 1) {
                  daysLabel.textContent = `${Math.floor(dayValue)} Day(s)`;
                } else if (decimal == .5 && numericDecimal <= 1) {
                  daysLabel.textContent = `${decimal * 8} Hour(s)`;
                }

                const decimalPart = numericDecimal - Math.floor(numericDecimal);

                if (integer === Math.floor(res)) {
                  manyHours.max = res * 8 - numericDecimal * 8;
                } else if (integer !== Math.floor(res) && decimalPart > 0) {
                  manyHours.max = (decimalPart * 8) - 1;
                } else if (integer !== Math.floor(res) && decimalPart === 0) {
                  manyHours.max = 7;
                }

                if (parseFloat(manyHours.value) >= parseFloat(manyHours.max)) {
                  manyHours.value = manyHours.max;
                  hoursLabel.textContent = `${manyHours.max} Hour(s)`;
                }
              });
            })
            .catch(error => {
              console.error("Terjadi error:", error);
            });
        });

        document.getElementById('startDate').addEventListener('click', () => {
          document.getElementById('startDate').showPicker();
        });
    </script>
    <x-contact/>
@endsection
