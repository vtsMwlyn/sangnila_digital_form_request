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

            <div class="col-span-3">
                <x-button type="button" id="submitBtn"
                    class="bg-blue-500 text-white px-4 py-2 rounded">
                    Fetch Attendance
                </x-button>
            </div>
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
        document.addEventListener("DOMContentLoaded", async function () {
            const startInput = document.getElementById("startDate");
            const endInput = document.getElementById("endDate");
            const resultContainer = document.getElementById('resultContainer');
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('global-loading');

            const now = new Date();

            // End date = 25th of current month
            const endDate = new Date(now.getFullYear(), now.getMonth(), 25);

            // Start date = 26th of previous month
            const startDate = new Date(now.getFullYear(), now.getMonth() - 1, 26);

            // Format to YYYY-MM-DD
            function formatInputDate(date) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, "0");
                const dd = String(date.getDate()).padStart(2, "0");
                return `${yyyy}-${mm}-${dd}`;
            }

            startInput.value = formatInputDate(startDate);
            endInput.value = formatInputDate(endDate);

            try {
                const employeesData = await fetch("https://cron.sangnilaindonesia.com/get-employees", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "x-api-key": "your_super_secret_key"
                    },
                });

                const result = await employeesData.json();

                if (!result.success || !result.data || result.data.length === 0) {
                    resultContainer.innerHTML = "<p>No data</p>";
                } else {
                    const selectEmployee = document.getElementById('keyword');
                    selectEmployee.innerHTML = '';

                    result.data.forEach(employee => {
                        const employeeOption = document.createElement('option');
                        employeeOption.setAttribute('value', employee.name);
                        employeeOption.innerText = employee.name;
                        selectEmployee.appendChild(employeeOption);
                    });
                }

            } catch (err) {
                console.error(err);
            }

            const formatDate = (dateStr) => {
                if (!dateStr) return "-";

                const d = new Date(dateStr);

                const days = [
                    "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
                ];

                const months = [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

                return days[d.getDay()] + ", " +
                    String(d.getDate()).padStart(2, "0") + " " +
                    months[d.getMonth()] + " " +
                    d.getFullYear();
            };

            const formatTime = (timeStr) => {
                if (!timeStr) return "-";

                const [h, m, s] = timeStr.split(":").map(Number);

                const date = new Date();
                date.setHours(h + 1);
                date.setMinutes(m);
                date.setSeconds(s || 0);

                return String(date.getHours()).padStart(2, "0") + ":" +
                    String(date.getMinutes()).padStart(2, "0");
            };

            submitBtn.addEventListener('click', async function(e) {
                e.preventDefault();

                if(loader) loader.classList.remove('hidden');

                submitBtn.innerText = "Loading...";
                submitBtn.disabled = true;

                const payload = {
                    keyword: document.getElementById('keyword').value,
                    startDate: document.getElementById('startDate').value,
                    endDate: document.getElementById('endDate').value
                };

                try {
                    // console.log('🕘 Fetching data...', payload);

                    const attendancesData = await fetch("https://cron.sangnilaindonesia.com/get-attendances", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "x-api-key": "your_super_secret_key"
                        },
                        body: JSON.stringify(payload)
                    });

                    const result = await attendancesData.json();

                    if (!result.success || !result.data || result.data.length === 0) {
                        resultContainer.innerHTML = "<p>No data</p>";

                        // console.log('🟡 No data found...');
                    } else {
                        let rows = "";

                        result.data.forEach((item, index) => {
                            rows += `
                                <tr class="${index % 2 === 0 ? 'bg-white' : ''}">
                                    <td class="py-3 px-4 text-start">${item.User?.name || 'N/A'}</td>
                                    <td class="py-3 px-4 text-start">${formatDate(item.date)}</td>
                                    <td class="py-3 px-4 text-start">${formatTime(item.checkIn)}</td>
                                    <td class="py-3 px-4 text-start">${formatTime(item.checkOut)}</td>
                                </tr>
                            `;
                        });

                        resultContainer.innerHTML = `
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
                        `;

                        // console.log('🟢 Success!');
                    }

                }
                catch (err) {
                    // console.error('🔴 Failed/Error', err);
                    resultContainer.innerHTML = "<p>Error fetching data</p>";
                }
                finally {
                    if(loader) loader.classList.add('hidden');

                    submitBtn.innerText = "Fetch Attendance";
                    submitBtn.disabled = false;
                }
            });
        });
    </script>
@endsection