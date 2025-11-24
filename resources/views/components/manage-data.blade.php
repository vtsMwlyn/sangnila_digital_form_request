<script>
    function clearFilters() {
        document.getElementById("search").value = "";
        document.getElementById("month").value = "all";

        const allDataButtons = document.querySelectorAll(
            'button[name="type"][value="all"]'
        );

        if (allDataButtons.length > 0) {
            allDataButtons[0].click();
        }

        const rows = document.querySelectorAll("tbody tr");
        rows.forEach((row) => {
            row.style.display = "";
        });
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('search').addEventListener("input", function () {
            let parentForm = this.closest('form')

            if (!this.value) {
                parentForm.submit()
                return
            }

            const query = new URLSearchParams(new FormData(parentForm)).toString();
            const newUrl = new URL(window.location);
            console.log(query);
            console.log(newUrl);
            newUrl.search = query;
            window.history.pushState({}, "", newUrl);

            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll("tbody tr");
            const isAdmin = "{{ auth()->user()->role }}" === "admin";
            const reasonIndex = isAdmin ? 4 : 3;

            rows.forEach((row) => {
                if (row.cells.length > 1) {
                    const reason = row.textContent.toLowerCase();
                    if (reason.includes(searchTerm)) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });
            if (rows[0].className === 'empty') {
                parentForm.submit();
                return
            }
        });

        document.querySelectorAll('.status-btn').forEach(s => {
            s.addEventListener('click', function() {
                    document.querySelectorAll('.buttonSubmit').forEach(b => {
                    b.value = this.value
                    b.closest('form').submit()
                });
            });
        });

        document.querySelectorAll(".eye-preview-btn").forEach((btn) => {
            btn.addEventListener("click", function () {
                console.log(this.dataset);

                const id = this.dataset.id;
                const date = this.dataset.date;
                const overworkDate = this.dataset.overwork_date;
                const start = this.dataset.start;
                const finish = this.dataset.finished;
                const type = this.dataset.type;
                const description = this.dataset.description;
                const status = this.dataset.status;
                const duration = this.dataset.duration;
                const adminNote = this.dataset.admin_note;
                const balance = this.dataset.balance;
                const overwork = this.dataset.overwork;
                const changeby = this.dataset.admin_change;
                const evidences = this.dataset.evidences
                    ? JSON.parse(this.dataset.evidences)
                    : [];
                const statusClass = getStatusClass(status);
                let rejectedOnly = "";
                let overworkOnly = "";


                if (type === "overwork") {
                    overworkOnly = `
                    <table class="w-full text-sm text-gray-800 border-collapse">
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">${type} Date:</th>
                            <td class="text-gray-900 py-2">${overworkDate}</td>
                        </tr>
                            `;
                }
                if (status === "rejected") {
                    rejectedOnly = `
                        <table class="w-full text-sm text-gray-800 border-collapse">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Reason For Rejection:</th>
                                <td class="text-gray-900 py-2 ${adminNote != '' ? '' : 'text-yellow-800'}">${adminNote != '' ? adminNote : '<i>(This request was rejected without a specified reason.</i> <br> <i>Please consult the admin if you wish to clarify further.)</i>'}</td>
                            </tr>
                            `;
                }
                let body = `
                    <table class="w-full text-sm text-gray-800 border-collapse">
                        <tbody class="divide-y divide-gray-200">

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Requested At:</th>
                            <td class="text-gray-900 py-2">${date}</td>
                        </tr>

                        ${overworkOnly}

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">
                                ${
                                type === "overwork"
                                ? `${type} time`
                                : `${type} date`
                                }
                            </th>
                            <td class="text-gray-900 py-2"> ${start}
                                    <i class="bi bi-arrow-right text-xl font-bold"></i>
                                ${finish}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Type:</th>
                            <td class="text-gray-900 py-2">${type}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Description:</th>
                            <td class="text-gray-900 py-2">${description.replace(
                                /\n/g,
                                "<br>"
                            )}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Duration:</th>
                            <td class="text-gray-900 py-2">${duration}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Leave Balance</th>
                            <td class="text-gray-900 py-2">${balance}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Total Overwork</th>
                            <td class="text-gray-900 py-2">${overwork}</td>
                        </tr>

                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Action by:</th>
                            <td class="text-gray-900 py-2">${changeby}</td>
                        </tr>

                        ${rejectedOnly}

                     </tbody>
                    </table>
                        `;
                body += `
                <table class="w-full text-sm text-gray-800 border-collapse">
                        <tbody class="divide-y divide-gray-200">

                         <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Status:</th>
                            <td class="${statusClass} mt-2 mb-2 py-1 px-3 inline-block rounded-full capitalize text-white">${status}</td>
                         </tr>
                        `;
                if (type === "overwork") {
                    body += `
                    <table class="w-full text-sm text-gray-800 border-collapse">
                        <tbody class="divide-y divide-gray-200">

                         <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Evidences:</th>
                            <td class="text-gray-900 py-2"> ${evidences
                                .map((e, index) => {
                                    const ext = e.path
                                        .split(".")
                                        .pop()
                                        .toLowerCase();
                                    if (
                                        [
                                            "jpg",
                                            "png",
                                            "jpeg",
                                            "webp",
                                        ].includes(ext)
                                    ) {
                                        return `<img src="/storage/${e.path}" alt="Evidence" class="xl:h-[200px] h-[100px] rounded shadow-sm cursor-pointer evidence-item" data-index="${index}">`;
                                    } else if (
                                        ["mp4", "mov", "avi"].includes(ext)
                                    ) {
                                        return `<video src="/storage/${e.path}" class="xl:h-[200px] h-[100px] rounded shadow-sm cursor-pointer evidence-item" data-index="${index}" controls></video>`;
                                    }
                                    return "";
                                })
                                .join("")}</td>
                         </tr>
                        `;
                }
                document.getElementById("dashboard-preview-body").innerHTML = body;
                currentEvidences = evidences;
                window.dispatchEvent(
                    new CustomEvent("open-modal", {
                        detail: "dashboard-preview-modal",
                    })
                );
            });
        });
    });

    function getStatusClass(status) {
        switch (status.toLowerCase()) {
            case "approved":
                return "bg-cyan-500 text-white rounded-full px-3 py-1 text-sm font-semibold";
            case "pending":
                return "bg-gray-400 text-white rounded-full px-3 py-1 text-sm font-semibold";
            case "rejected":
                return "bg-red-500 text-white rounded-full px-3 py-1 text-sm font-semibold";
            default:
                return "bg-gray-500 text-white capitalize rounded-full px-3 py-1 text-sm font-semibold";
        }
    }

    let currentEvidences = [];
    let currentIndex = 0;

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("evidence-item")) {
            const index = parseInt(e.target.dataset.index);
            currentIndex = index;
            showEvidence(currentIndex);
            window.dispatchEvent(
                new CustomEvent("open-modal", {
                    detail: "evidence-viewer-modal",
                })
            );
        }
    });

    function showEvidence(index) {
        const e = currentEvidences[index];
        const ext = e.path.split(".").pop().toLowerCase();
        let mediaHtml = "";
        if (["jpg", "png", "jpeg", "webp"].includes(ext)) {
            mediaHtml = `<img src="/storage/${e.path}" alt="Evidence" class="max-w-full h-[600px] rounded shadow-lg">`;
        } else if (["mp4", "mov", "avi"].includes(ext)) {
            mediaHtml = `<video src="/storage/${e.path}" class="max-w-full h-[600px] rounded shadow-lg" controls autoplay></video>`;
        }
        document.getElementById("evidence-viewer-body").innerHTML = mediaHtml;
        document.getElementById("prev-evidence").style.display =
            index > 0 ? "block" : "none";
        document.getElementById("next-evidence").style.display =
            index < currentEvidences.length - 1 ? "block" : "none";
    }

    document
        .getElementById("prev-evidence")
        .addEventListener("click", function () {
            if (currentIndex > 0) {
                currentIndex--;
                showEvidence(currentIndex);
            }
        });

    document
        .getElementById("next-evidence")
        .addEventListener("click", function () {
            if (currentIndex < currentEvidences.length - 1) {
                currentIndex++;
                showEvidence(currentIndex);
            }
        });

        document.querySelectorAll('.rejectButton').forEach(b => {
            b.addEventListener('click', function () {
                const value = this.getAttribute('value');
                const form = this.closest('form');

                const rejectedInput = document.getElementById('rejectedValue');
                const noteInput = document.getElementById('adminNoteInput');

                if (!rejectedInput || !noteInput) {
                    alert('Reject modal not found. Please ensure it is included in the layout.');
                    return;
                }

                rejectedInput.value = value;
                window.currentRejectForm = form;

                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'reject-modal' }));
            });
        });

        document.getElementById('rejectForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const note = document.getElementById('adminNoteInput').value.trim();
            if (!note) return alert('Please enter a reason.');

            const form = window.currentRejectForm;

            const adminNote = document.createElement('input');
            const statusData = document.createElement('input');

            adminNote.type = 'hidden';
            adminNote.name = 'admin_note';
            adminNote.value = note;

            statusData.type = 'hidden';
            statusData.name = 'rejected';
            statusData.value = document.getElementById('rejectedValue').value;

            form.appendChild(adminNote);
            form.appendChild(statusData);

            form.submit();
            window.dispatchEvent(new CustomEvent('close-modal', { detail: 'reject-modal' }));
            document.getElementById('adminNoteInput').value = '';
        });

        function openChooseModal(button) {
            console.log($(button).data());
            const leaveId = $(button).data('leaveid');
            const leavePeriod = $(button).data('leaveperiod');
            const user = $(button).data('user');

            leaveDays = Math.floor(user.leave_balance / 8);
            overworkDays = Math.floor(user.overwork_balance / 8);
            requestedDays = Math.floor(parseFloat(leavePeriod) / 8);

            leaveHours = user.leave_balance - (leaveDays * 8);
            overworkHours = user.overwork_balance - (overworkDays * 8);
            requestedHours = leavePeriod - (requestedDays * 8);

            $('input[name="leaveId"]').val(leaveId);
            $('#choose-modal-requested').text(`${requestedDays} day(s) ${requestedHours} hour(s)`);
            $('#choose-modal-user-leave-balance').text(`Available: ${leaveDays} day(s) ${leaveHours} hour(s)`);
            $('#choose-modal-user-overwork-balance').text(`Available: ${overworkDays} day(s) ${overworkHours} hour(s)`);

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'choose-modal' }));
        }

        function openEditModal(button) {
            const id = button.getAttribute('data-id');

            const row = button.closest('tr');
            const name = row.querySelector('.user-name').innerText;
            const email = row.querySelector('.user-email').innerText;
            const phone = row.querySelector('.user-phone').innerText;
            const leave = row.querySelector('.user-leave').innerText;
            const overwork = row.querySelector('.user-overwork').innerText;
            const position = row.querySelector('.user-position').innerText;
            const department = row.querySelector('.user-department').innerText;

            document.getElementById('user_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('email').value = email;
            document.getElementById('phone').value = phone;
            document.getElementById('Leave_Balance').value = leave;
            document.getElementById('overwork_balance').value = overwork;
            document.getElementById('positionSelect').value = position;
            document.getElementById('departmentSelect').value = department;

            const form = document.getElementById('editForm');
            form.action = `/update/${id}`;

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'edit-modal' }));
        }
</script>

