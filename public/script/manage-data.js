function clearFilters() {
    $("#search").val("");
    $("#month").val("all");

    const $allDataButtons = $('button[name="type"][value="all"]');
    if ($allDataButtons.length > 0) {
        $allDataButtons.eq(0).trigger("click");
    }

    $("tbody tr").css("display", "");
}

$(document).ready(function () {

    // status button submit
    $('.status-btn').on('click', function () {
        $('.buttonSubmit').each(function () {
            $(this).val($(this).closest('form').find('.status-btn').val());
            $(this).closest('form').submit();
        });
    });

    // preview modal
    $('.eye-preview-btn').on('click', function () {
        const data = $(this).data();

        const id = data.id;
        const date = data.date;
        const overtimeDate = data.overtime_date;
        const start = data.start;
        const finish = data.finished;
        const type = data.type;
        const description = data.description;
        const status = data.status;
        const duration = data.duration;
        const adminNote = data.admin_note;
        const balance = data.balance;
        const overtime = data.overtime;
        const changeby = data.admin_change;
        const evidences = data.evidences ? JSON.parse(data.evidences) : [];

        const statusClass = getStatusClass(status);

        let rejectedOnly = "";
        let overtimeOnly = "";

        if (type === "overtime") {
            overtimeOnly = `
                <table class="w-full text-sm text-gray-800 border-collapse">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">${type} Date:</th>
                            <td class="text-gray-900 py-2">${overtimeDate}</td>
                        </tr>`;
        }

        if (status === "rejected") {
            rejectedOnly = `
                <table class="w-full text-sm text-gray-800 border-collapse">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Reason For Rejection:</th>
                            <td class="text-gray-900 py-2 ${adminNote != '' ? '' : 'text-yellow-800'}">
                                ${adminNote != '' ? adminNote : '<i>(This request was rejected without a specified reason.</i><br><i>Please consult the admin if you wish to clarify further.)</i>'}
                            </td>
                        </tr>`;
        }

        let body = `
            <table class="w-full text-sm text-gray-800 border-collapse">
                <tbody class="divide-y divide-gray-200">

                <tr>
                    <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Requested At:</th>
                    <td class="text-gray-900 py-2">${date}</td>
                </tr>

                ${overtimeOnly}

                <tr>
                    <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">
                        ${type === "overtime" ? `${type} time` : `${type} date`}
                    </th>
                    <td class="text-gray-900 py-2">${start}
                        <i class="bi bi-arrow-right text-xl font-bold"></i>
                        ${finish}
                    </td>
                </tr>

                <tr>
                    <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Type:</th>
                    <td class="text-gray-900 py-2">${type}</td>
                </tr>

                <tr>
                    <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Description:</th>
                    <td class="text-gray-900 py-2">${description.replace(/\n/g, "<br>")}</td>
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
                    <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Total Overtime</th>
                    <td class="text-gray-900 py-2">${overtime}</td>
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
                <tbody>
                    <tr>
                        <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Status:</th>
                        <td class="${statusClass} mt-2 mb-2 py-1 px-3 inline-block rounded-full capitalize text-white">${status}</td>
                    </tr>
        `;

        if (type === "overtime") {
            body += `
                <tr>
                    <th class="text-left font-semibold text-gray-700 py-2 pr-4 w-1/3">Evidences:</th>
                    <td>
                        ${evidences.map((e, index) => {
                            const ext = e.path.split('.').pop().toLowerCase();
                            if (["jpg","png","jpeg","webp"].includes(ext)) {
                                return `<img src="/storage/${e.path}" class="xl:h-[200px] h-[100px] rounded shadow-sm cursor-pointer evidence-item" data-index="${index}">`;
                            } else if (["mp4","mov","avi"].includes(ext)) {
                                return `<video src="/storage/${e.path}" class="xl:h-[200px] h-[100px] rounded shadow-sm cursor-pointer evidence-item" data-index="${index}" controls></video>`;
                            }
                            return "";
                        }).join("")}
                    </td>
                </tr>`;
        }

        $("#dashboard-preview-body").html(body);
        currentEvidences = evidences;

        window.dispatchEvent(new CustomEvent("open-modal", {
            detail: "dashboard-preview-modal"
        }));
    });

    // evidence click (delegated)
    $(document).on("click", ".evidence-item", function () {
        currentIndex = parseInt($(this).data("index"));
        showEvidence(currentIndex);

        window.dispatchEvent(new CustomEvent("open-modal", {
            detail: "evidence-viewer-modal"
        }));
    });

    // prev / next
    $("#prev-evidence").on("click", function () {
        if (currentIndex > 0) {
            currentIndex--;
            showEvidence(currentIndex);
        }
    });

    $("#next-evidence").on("click", function () {
        if (currentIndex < currentEvidences.length - 1) {
            currentIndex++;
            showEvidence(currentIndex);
        }
    });

    // reject button
    $('.rejectButton').on('click', function () {
        const value = $(this).val();
        const form = $(this).closest('form');

        const rejectedInput = $('#rejectedValue');
        const noteInput = $('#adminNoteInput');

        if (!rejectedInput.length || !noteInput.length) {
            alert('Reject modal not found.');
            return;
        }

        rejectedInput.val(value);
        window.currentRejectForm = form;

        window.dispatchEvent(new CustomEvent('open-modal', {
            detail: 'reject-modal'
        }));
    });

    // reject submit
    $('#rejectForm').on('submit', function (e) {
        e.preventDefault();

        const note = $('#adminNoteInput').val().trim();
        if (!note) return alert('Please enter a reason.');

        const form = window.currentRejectForm;

        $('<input>', {
            type: 'hidden',
            name: 'admin_note',
            value: note
        }).appendTo(form);

        $('<input>', {
            type: 'hidden',
            name: 'rejected',
            value: $('#rejectedValue').val()
        }).appendTo(form);

        form.submit();

        window.dispatchEvent(new CustomEvent('close-modal', {
            detail: 'reject-modal'
        }));

        $('#adminNoteInput').val('');
    });
});

function getStatusClass(status) {
    switch (status.toLowerCase()) {
        case "approved": return "bg-cyan-500 text-white rounded-full px-3 py-1 text-sm font-semibold";
        case "pending": return "bg-gray-400 text-white rounded-full px-3 py-1 text-sm font-semibold";
        case "rejected": return "bg-red-500 text-white rounded-full px-3 py-1 text-sm font-semibold";
        default: return "bg-gray-500 text-white rounded-full px-3 py-1 text-sm font-semibold";
    }
}

let currentEvidences = [];
let currentIndex = 0;

function showEvidence(index) {
    const e = currentEvidences[index];
    const ext = e.path.split(".").pop().toLowerCase();

    let mediaHtml = "";
    if (["jpg","png","jpeg","webp"].includes(ext)) {
        mediaHtml = `<img src="/storage/${e.path}" class="max-w-full h-[600px] rounded shadow-lg">`;
    } else if (["mp4","mov","avi"].includes(ext)) {
        mediaHtml = `<video src="/storage/${e.path}" class="max-w-full h-[600px]" controls autoplay></video>`;
    }

    $("#evidence-viewer-body").html(mediaHtml);
    $("#prev-evidence").toggle(index > 0);
    $("#next-evidence").toggle(index < currentEvidences.length - 1);
}

function openChooseModal(button) {
    const data = $(button).data();

    const leaveId = data.leaveid;
    const leavePeriod = data.leaveperiod;
    const user = data.user;

    let leaveDays = Math.floor(user.leave_balance / 8);
    let overtimeDays = Math.floor(user.overtime_balance / 8);
    let requestedDays = Math.floor(parseFloat(leavePeriod) / 8);

    let leaveHours = user.leave_balance - (leaveDays * 8);
    let overtimeHours = user.overtime_balance - (overtimeDays * 8);
    let requestedHours = leavePeriod - (requestedDays * 8);

    $('input[name="leaveId"]').val(leaveId);
    $('#choose-modal-requested').text(`${requestedDays} day(s) ${requestedHours} hour(s)`);
    $('#choose-modal-user-leave-balance').text(`Available: ${leaveDays} day(s) ${leaveHours} hour(s)`);
    $('#choose-modal-user-overtime-balance').text(`Available: ${overtimeDays} day(s) ${overtimeHours} hour(s)`);

    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: 'choose-modal'
    }));
}

function openEditModal(button) {
    const $row = $(button).closest('tr');

    $('#user_id').val($(button).data('id'));
    $('#name').val($row.find('.user-name').text());
    $('#email').val($row.find('.user-email').text());
    $('#phone').val($row.find('.user-phone').text());
    $('#Leave_Balance').val($row.find('.user-leave').text());
    $('#overtime_balance').val($row.find('.user-overtime').text());
    $('#positionSelect').val($row.find('.user-position').text());
    $('#departmentSelect').val($row.find('.user-department').text());

    $('#editForm').attr('action', `/update/${$(button).data('id')}`);

    window.dispatchEvent(new CustomEvent('open-modal', {
        detail: 'edit-modal'
    }));
}
