@extends('layouts.app')

@section('content')
    <section class="mx-1 container-draft bg-[#F0F3F8] p-6 rounded-2xl w-full shadow-lg overflow-x-auto">

        <div class="w-full grid grid-cols-3 gap-4">
            
            {{-- Employee Name --}}
            <div class="w-full grid mb-4">
                <label>Employee Name</label>
                <x-select type="text" id="keyword" class="mt-1 block w-full border rounded px-2 py-1" >
                    <option disabled>Fetching employee...</option>
                </x-select>
            </div>

            {{-- Start Date --}}
            <div class="w-full grid mb-4">
                <label>Start Date</label>
                <x-text-input type="date" onclick="this.showPicker()" id="startDate" class="mt-1 block w-full border rounded px-2 py-1" />
            </div>

            {{-- End Date --}}
            <div class="w-full grid mb-4">
                <label>End Date</label>
                <x-text-input type="date" onclick="this.showPicker()" id="endDate" class="mt-1 block w-full border rounded px-2 py-1" />
            </div>
        </div>

        <div class="w-full mb-4 flex justify-between items-center">
            <div class="flex gap-6">
                <label for="isRamadhan" class="flex items-center gap-2">
                    <input type="checkbox" id="isRamadhan">
                    <p>Is Ramadhan Month</p>
                </label>

                <label for="fridayFellowship" class="flex items-center gap-2">
                    <input type="checkbox" id="fridayFellowship">
                    <p>Friday has Fellowship</p>
                </label>
            </div>

            <x-button type="button" id="submitBtn"
                class="bg-blue-500 text-white px-4 py-2 rounded">
                Fetch Attendance
            </x-button>
        </div>

        {{-- Result --}}
        <div class="mt-6 w-full">
            <h2 class="font-bold mb-2">Results:</h2>

            <div id="resultContainer">
                <p>No data</p>
            </div>
        </div>

    </section>

    <script>
        $(document).ready(function () {

            const $startInput = $("#startDate");
            const $endInput = $("#endDate");
            const $resultContainer = $("#resultContainer");
            const $submitBtn = $("#submitBtn");
            const $loader = $("#global-loading");
            const $keyword = $("#keyword");
            const $isRamadhan = $('#isRamadhan');
            const $fridayFellowship = $('#fridayFellowship');

            // ===== UTILS =====

            function formatInputDate(date) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, "0");
                const dd = String(date.getDate()).padStart(2, "0");
                return `${yyyy}-${mm}-${dd}`;
            }

            function formatDate(dateStr) {
                if (!dateStr) return "-";

                const d = new Date(dateStr);

                const days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
                const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

                return `${days[d.getDay()]}, ${String(d.getDate()).padStart(2, "0")} ${months[d.getMonth()]} ${d.getFullYear()}`;
            }

            function formatTime(timeStr) {
                if (!timeStr || timeStr === '—') return "—";

                const [h, m, s] = timeStr.split(":").map(Number);

                const date = new Date();
                date.setHours(h + 1);
                date.setMinutes(m);
                date.setSeconds(s || 0);

                return `${String(date.getHours()).padStart(2, "0")}:${String(date.getMinutes()).padStart(2, "0")}`;
            }

            function checkLateness(date, checkInTime) {
                if (!checkInTime) return false;

                const [h, m] = checkInTime.split(":").map(Number);

                const ramadhan = $isRamadhan.is(':checked');
                const hasFellowship = $fridayFellowship.is(':checked');

                const isFriday = new Date(date).getDay() === 5;

                const isSpecialDay = ramadhan || (hasFellowship && isFriday);

                // adjust timezone if needed
                const hour = h + 1;

                const totalMinutes = hour * 60 + m;

                const cutoff = (isSpecialDay ? 8 : 9) * 60 + 15;

                return totalMinutes > cutoff;
            }

            function normalizeAttendance(date, checkIn, checkOut) {
                if (!checkIn || !checkOut) {
                    return {
                        checkIn: checkIn || "—",
                        checkOut: checkOut || "—"
                    };
                }

                if (checkIn === checkOut) {
                    const [h, m] = checkIn.split(":").map(Number);
                    const totalMinutes = h * 60 + m;

                    const ramadhan = $isRamadhan.is(':checked');
                    const hasFellowship = $fridayFellowship.is(':checked');
                    const isFriday = new Date(date).getDay() === 5;

                    const isSpecialDay = ramadhan || (hasFellowship && isFriday);

                    // dynamic threshold
                    const threshold = (isSpecialDay ? 12 : 13) * 60;

                    if (totalMinutes < threshold) {
                        return {
                            checkIn: checkIn,
                            checkOut: "—"
                        };
                    } else {
                        return {
                            checkIn: "—",
                            checkOut: checkOut
                        };
                    }
                }

                return { checkIn, checkOut };
            }

            // ===== INIT DATE =====

            const now = new Date();

            const endDate = new Date(now.getFullYear(), now.getMonth(), 25);
            const startDate = new Date(now.getFullYear(), now.getMonth() - 1, 26);

            $startInput.val(formatInputDate(startDate));
            $endInput.val(formatInputDate(endDate));

            // ===== FETCH EMPLOYEES =====

            $.ajax({
                url: "https://cron.sangnilaindonesia.com/get-employees",
                method: "POST",
                headers: {
                    "x-api-key": "your_super_secret_key"
                },
                success: function (result) {

                    if (!result.success || !result.data || result.data.length === 0) {
                        $resultContainer.html("<p>No data</p>");
                        return;
                    }

                    $keyword.empty();

                    result.data.forEach(emp => {
                        $keyword.append(
                            `<option value="${emp.name}">${emp.name}</option>`
                        );
                    });
                },
                error: function (err) {
                    console.error(err);
                }
            });

            // ===== SUBMIT =====

            $submitBtn.on("click", function (e) {
                e.preventDefault();

                $loader.removeClass("hidden");

                $submitBtn.text("Loading...").prop("disabled", true);

                const payload = {
                    keyword: $keyword.val(),
                    startDate: $startInput.val(),
                    endDate: $endInput.val()
                };

                $.ajax({
                    url: "https://cron.sangnilaindonesia.com/get-attendances",
                    method: "POST",
                    headers: {
                        "x-api-key": "your_super_secret_key"
                    },
                    contentType: "application/json",
                    data: JSON.stringify(payload),

                    success: function (result) {

                        if (!result.success || !result.data || result.data.length === 0) {
                            $resultContainer.html("<p>No data</p>");
                            return;
                        }

                        let rows = "";

                        result.data.forEach((item, index) => {
                            const isLate = checkLateness(item.date, item.checkIn);
                            const normalized = normalizeAttendance(item.date, item.checkIn, item.checkOut);
                            const missingTimestamp = normalized.checkIn === '—' || normalized.checkOut === '—';

                            rows += `
                                <tr class="${isLate ? 'bg-red-200/50 text-red-600' : (missingTimestamp ? 'bg-yellow-200/50 text-amber-600' : (index % 2 === 0 ? 'bg-white' : ''))}">
                                    <td class="py-3 px-4 text-start">${item.User?.name || 'N/A'}</td>
                                    <td class="py-3 px-4 text-start">${formatDate(item.date)}</td>
                                    <td class="py-3 px-4 text-start">${formatTime(normalized.checkIn)}</td>
                                    <td class="py-3 px-4 text-start">${formatTime(normalized.checkOut)}</td>
                                </tr>
                            `;
                        });

                        $resultContainer.html(`
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr>
                                            <th class="border-b-2 border-slate-400 text-start">Employee</th>
                                            <th class="border-b-2 border-slate-400 text-start">Date</th>
                                            <th class="border-b-2 border-slate-400 text-start">Check In</th>
                                            <th class="border-b-2 border-slate-400 text-start">Check Out</th>
                                        </tr>
                                    </thead>
                                    <tbody>${rows}</tbody>
                                </table>
                            </div>
                        `);
                    },

                    error: function (err) {
                        console.error(err);
                        $resultContainer.html("<p>Error fetching data</p>");
                    },

                    complete: function () {
                        $loader.addClass("hidden");
                        $submitBtn.text("Fetch Attendance").prop("disabled", false);
                    }
                });
            });

        });
    </script>
@endsection