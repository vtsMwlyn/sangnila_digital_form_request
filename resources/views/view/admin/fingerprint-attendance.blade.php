@extends('layouts.app')

@section('content')
<section class="mx-1 container-draft bg-[#F0F3F8] p-6 rounded-2xl w-full shadow-lg overflow-x-auto">

    <form id="filterForm" class="w-full grid grid-cols-3 gap-4">
        
        {{-- Employee Name --}}
        <div class="w-full grid mb-4">
            <label>Employee Name</label>
            <x-text-input type="text" id="keyword" class="mt-1 block w-full border rounded px-2 py-1" />
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
            <x-button type="submit" id="submitBtn"
                class="bg-blue-500 text-white px-4 py-2 rounded">
                Fetch Attendance
            </x-button>
        </div>
    </form>

    {{-- Result --}}
    <div class="mt-6 w-full">
        <h2 class="font-bold mb-2">Results:</h2>

        <div id="resultContainer">
            <p>No data</p>
        </div>
    </div>

</section>

<script>
    const form = document.getElementById('filterForm');
    const resultContainer = document.getElementById('resultContainer');
    const submitBtn = document.getElementById('submitBtn');

    const formatDate = (dateStr) => {
        if (!dateStr) return "-";

        const d = new Date(dateStr);

        const months = [
            "Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        return String(d.getDate()).padStart(2, "0") + " " +
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

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        submitBtn.innerText = "Loading...";
        submitBtn.disabled = true;

        const payload = {
            keyword: document.getElementById('keyword').value,
            startDate: document.getElementById('startDate').value,
            endDate: document.getElementById('endDate').value
        };

        try {
            const res = await fetch("https://cron.sangnilaindonesia.com/get-attendances", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "x-api-key": "your_super_secret_key"
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (!result.success || result.data.length === 0) {
                resultContainer.innerHTML = "<p>No data</p>";
            } else {
                let rows = "";

                result.data.forEach((item, index) => {
                    rows += `
                        <tr class="${index % 2 === 0 ? 'bg-white' : ''}">
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
                                    <th class="border-b-2 border-slate-400 text-start">Date</th>
                                    <th class="border-b-2 border-slate-400 text-start">Check In</th>
                                    <th class="border-b-2 border-slate-400 text-start">Check Out</th>
                                </tr>
                            </thead>
                            <tbody>${rows}</tbody>
                        </table>
                    </div>
                `;

                const loader = document.getElementById('global-loading');
                loader.classList.add('hidden');
            }

        } catch (err) {
            console.error(err);
            resultContainer.innerHTML = "<p>Error fetching data</p>";
        } finally {
            submitBtn.innerText = "Fetch Attendance";
            submitBtn.disabled = false;
        }
    });
</script>
@endsection