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
            <h2 class="font-bold mb-4">Attendance Calendar:</h2>
            
            {{-- Replaced the simple text with a calendar container --}}
            <div id="calendarContainer" class="bg-white p-4 rounded-xl shadow-sm">
                <div id="calendar"></div>
            </div>
        </div>

    </section>

    <script>
        $(document).ready(function () {
            const $startInput = $("#startDate");
            const $endInput = $("#endDate");
            const $submitBtn = $("#submitBtn");
            const $loader = $("#global-loading");
            const $keyword = $("#keyword");
            const $isRamadhan = $('#isRamadhan');
            const $fridayFellowship = $('#fridayFellowship');

            // ===== INITIALIZE FULLCALENDAR =====
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                height: 'auto',
                events: [] // We will populate this via AJAX
            });
            calendar.render();

            // ===== UTILS =====
            function formatInputDate(date) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, "0");
                const dd = String(date.getDate()).padStart(2, "0");
                return `${yyyy}-${mm}-${dd}`;
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

                const hour = h + 1;
                const totalMinutes = hour * 60 + m;
                const cutoff = (isSpecialDay ? 8 : 9) * 60 + 15;
                return totalMinutes > cutoff;
            }

            function normalizeAttendance(date, checkIn, checkOut) {
                if (!checkIn || !checkOut) {
                    return { checkIn: checkIn || "—", checkOut: checkOut || "—" };
                }

                if (checkIn === checkOut) {
                    const [h, m] = checkIn.split(":").map(Number);
                    const totalMinutes = h * 60 + m;
                    const ramadhan = $isRamadhan.is(':checked');
                    const hasFellowship = $fridayFellowship.is(':checked');
                    const isFriday = new Date(date).getDay() === 5;
                    const isSpecialDay = ramadhan || (hasFellowship && isFriday);
                    const threshold = (isSpecialDay ? 12 : 13) * 60;

                    if (totalMinutes < threshold) {
                        return { checkIn: checkIn, checkOut: "—" };
                    } else {
                        return { checkIn: "—", checkOut: checkOut };
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

            // Move calendar to the start date initially
            calendar.gotoDate(startDate);

            // ===== FETCH EMPLOYEES =====
            $.ajax({
                url: "https://cron.sangnilaindonesia.com/get-employees",
                method: "POST",
                headers: { "x-api-key": "your_super_secret_key" },
                success: function (result) {
                    if (!result.success || !result.data || result.data.length === 0) return;
                    $keyword.empty();
                    result.data.forEach(emp => {
                        $keyword.append(`<option value="${emp.name}">${emp.name}</option>`);
                    });
                },
                error: function (err) { console.error(err); }
            });

            // ===== SUBMIT =====
            $submitBtn.on("click", function (e) {
                e.preventDefault();
                if($loader.length) $loader.removeClass("hidden");
                $submitBtn.text("Loading...").prop("disabled", true);

                const payload = {
                    keyword: $keyword.val(),
                    startDate: $startInput.val(),
                    endDate: $endInput.val()
                };

                $.ajax({
                    url: "https://cron.sangnilaindonesia.com/get-attendances",
                    method: "POST",
                    headers: { "x-api-key": "your_super_secret_key" },
                    contentType: "application/json",
                    data: JSON.stringify(payload),
                    success: function (result) {
                        // Clear existing events first
                        calendar.removeAllEvents();

                        if (!result.success || !result.data || result.data.length === 0) {
                            alert("No data found for this period.");
                            return;
                        }

                        let calendarEvents = [];

                        result.data.forEach((item) => {
                            const isLate = checkLateness(item.date, item.checkIn);
                            const normalized = normalizeAttendance(item.date, item.checkIn, item.checkOut);
                            const missingTimestamp = normalized.checkIn === '—' || normalized.checkOut === '—';

                            // Determine event color based on your original logic
                            let bgColor = '#16a34a'; // Tailwind blue-500 (default)
                            if (isLate) {
                                bgColor = '#ef4444'; // Tailwind red-500
                            } else if (missingTimestamp) {
                                bgColor = '#f59e0b'; // Tailwind amber-500
                            }

                            // Create the FullCalendar event object
                            calendarEvents.push({
                                title: `In: ${formatTime(normalized.checkIn)} • Out: ${formatTime(normalized.checkOut)}`,
                                start: item.date, // YYYY-MM-DD places it on the correct day
                                allDay: true,     // Renders as a solid block on the day grid
                                backgroundColor: bgColor,
                                borderColor: bgColor,
                                textColor: '#ffffff'
                            });
                        });

                        // Add new events to calendar and jump to the fetched month
                        calendar.addEventSource(calendarEvents);
                        calendar.gotoDate(payload.startDate);
                    },
                    error: function (err) {
                        console.error(err);
                        alert("Error fetching data");
                    },
                    complete: function () {
                        if($loader.length) $loader.addClass("hidden");
                        $submitBtn.text("Fetch Attendance").prop("disabled", false);
                    }
                });
            });
        });
    </script>
@endsection