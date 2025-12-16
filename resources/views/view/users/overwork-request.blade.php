@extends('layouts.app')

@section('content')
    @php
        $existingEvidences = [];

        if (isset($overwork)) {
            foreach ($overwork->evidence as $e) {
                $ext = strtolower(pathinfo($e->path, PATHINFO_EXTENSION));

                $existingEvidences[] = [
                    'id'   => $e->id,
                    'type' => in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])
                                ? 'photo'
                                : 'video',
                    'path' => asset('storage/' . $e->path),
                ];
            }
        }
    @endphp

    <form
        action="{{ isset($overwork) ? route('overwork.update', $overwork) : route('overwork.insert') }}"
        method="post"
        enctype="multipart/form-data"
        class="container-draft bg-[#FEFEFEB2] p-8 rounded-2xl w-full shadow-lg overflow-x-auto"
    >
        @csrf
        @if(isset($overwork))
            @method('PUT')
        @endif

        <x-back-button onclick="history.back();" />

        <h2 class="text-[#042E66] text-3xl font-black mt-2 mb-1">
            {{ isset($overwork) ? 'Edit Overwork Draft' : 'New Overwork Request' }}
        </h2>
        <span class="text-slate-500 italic">Pengajuan Lembur</span>
        <x-separator-line/>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-10 mt-5">
            {{-- ================= Submission Section ================= --}}
            <div class="flex-1">
                <h3 class="text-blue-800 font-extrabold text-lg mb-4">
                    Employee Information
                </h3>

                <x-submisson />

                {{-- Media Preview --}}
                <div id="media-preview" class="mt-12 hidden">
                    <h3 class="text-blue-800 font-extrabold text-lg mb-4">
                        Selected Files Preview
                    </h3>
                    <div id="preview-images-existing" class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-2 w-full"></div>
                    <div id="preview-images-new" class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-2 w-full"></div>

                    <div id="preview-videos-existing" class="grid grid-cols-1 xl:grid-cols-2 gap-6 w-full"></div>
                    <div id="preview-videos-new" class="grid grid-cols-1 xl:grid-cols-2 gap-6 w-full"></div>

                </div>
            </div>

            {{-- ================= Overwork Section ================= --}}
            <div class="flex-1 flex flex-col w-full">
                <h3 class="text-blue-800 font-extrabold text-lg xl:mt-0 mt-2">Overwork Information</h3>

                {{-- Date Input --}}
                <div class="w-full relative mt-4">
                    <x-input-label for="date" class="font-black text-[16px] mb-2">
                        Date: <span class="text-red-500">*</span>
                    </x-input-label>

                    <x-text-input
                        type="date"
                        name="date"
                        id="date"
                        value="{{ old('date', isset($overwork) ? $overwork->overwork_date : '') }}"
                        class="w-full"
                        onclick="this.showPicker();"
                    />

                    <x-unvalid-input field="date" />
                </div>

                {{-- Start & Finish Time --}}
                <div class="flex mt-3 items-center w-full gap-4">
                    <div class="w-full relative">
                        <x-input-label for="start" class="font-black text-[16px] mb-1">
                            Start: <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input
                            type="time"
                            name="start"
                            id="start"
                            value="{{ old('start', isset($overwork) ? $overwork->start_overwork : '17:00') }}"
                            class="border-2 w-full"
                            />
                            <x-unvalid-input field="start" />
                    </div>

                    <i class="bi bi-arrow-right mt-8 text-2xl text-gray-500"></i>

                    <div class="w-full relative">
                        <x-input-label for="finish" class="font-black text-[16px] mb-1">
                            Finish: <span class="text-red-500">*</span>
                        </x-input-label>
                        <x-text-input
                            type="time"
                            name="finish"
                            id="finish"
                            value="{{ old('finish', isset($overwork) ? $overwork->finished_overwork : '') }}"
                            class="border-2 w-full"
                            required
                        />
                        <x-unvalid-input field="finish" />
                    </div>
                </div>

                {{-- Description --}}
                <div class="mt-4">
                    <x-input-label for="desc" class="font-black text-[16px] mb-1">
                        Task Description: <span class="text-red-500">*</span>
                    </x-input-label>
                    <x-textarea
                        name="desc"
                        id="desc"
                        rows="4"
                        placeholder="Describe or list the tasks you did for this overwork"
                        required
                        class="w-full"
                    >
                        {{ old('desc', isset($overwork) ? $overwork->task_description : '') }}
                    </x-textarea>
                </div>

                {{-- Upload Inputs --}}
                <p class="text-gray-500 mt-6">Please upload a photo or video evidence for validation<span class="text-red-500">*</span><br/><span class="font-bold">(You may select files, drag and drop your files, or paste them using Ctrl/Cmd + V)</span></p>
                <div class="mt-4 grid grid-cols-1 xl:grid-cols-2 gap-5">
                    <div class="pl-2">
                        <label>Photo:</label><br />
                        <x-text-input type="file" name="photo[]" multiple id="photo-input" accept="image/*" class="w-full py-20 px-10 border-4 rounded-xl border-slate-400 border-dashed bg-white" />
                        @error('photo')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="pl-2">
                        <label>Video:</label><br />
                        <x-text-input type="file" name="video[]" multiple id="video-input" accept="video/*" class="w-full py-20 px-10 border-4 rounded-xl border-slate-400 border-dashed bg-white" />
                        @error('video')
                            <div class="text-red-500 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <span class="text-slate-600 italic mt-10">By submitting, you confirm that all provided information is accurate and you acknowledge that <b>any false or invalid data may result in consequences</b>.</span>

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

    {{-- ================= Evidence Viewer Modal ================= --}}
    <x-modal name="evidence-viewer-modal" maxWidth="6xl">
        <div class="flex items-center justify-center relative p-6">
            <button
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'evidence-viewer-modal' }))"
                class="absolute right-5 top-0 m-5 text-red-500 hover:text-red-300 text-3xl"
            >
                <img src="{{ asset('img/close.svg') }}" alt="x" />
            </button>

            <div id="evidence-viewer-body" class="flex items-center justify-center"></div>

            <button id="prev-evidence" class="absolute left-4 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">&larr;</button>
            <button id="next-evidence" class="absolute right-4 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded">&rarr;</button>
        </div>
    </x-modal>

    {{-- ================= JS Section ================= --}}
    @if(isset($overwork))
        <script>
            window.existingEvidences = @json($existingEvidences);
        </script>
    @else
        <script>
            window.existingEvidences = [];
        </script>
    @endif

    <script>
        /* ================= STATE ================= */
        let photos = [];
        let videos = [];

        /* ================= SYNC FILE INPUT ================= */
        function syncInput(type) {
            const dt = new DataTransfer();
            const files = type === "photo" ? photos : videos;
            files.forEach(f => dt.items.add(f));
            document.getElementById(type + "-input").files = dt.files;
        }

        /* ================= PREVIEW ================= */
        function refreshPreview(type) {
            const container = document.getElementById(
                type === "photo" ? "preview-images-new" : "preview-videos-new"
            );

            const files = type === "photo" ? photos : videos;
            container.innerHTML = "";

            files.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement("div");
                    div.className = "relative group";
                    div.innerHTML = `
                        <div class="h-[150px] rounded overflow-hidden border">
                            ${
                                type === "photo"
                                    ? `<img src="${e.target.result}" class="w-full h-full object-cover">`
                                    : `<video src="${e.target.result}" muted controls class="w-full h-full object-cover"></video>`
                            }
                        </div>
                        <button type="button"
                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 text-xs remove-file"
                            data-type="${type}" data-index="${i}">×</button>
                    `;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            togglePreview();
        }


        /* ================= EXISTING EVIDENCE ================= */
        function renderExistingEvidence() {
            if (!window.existingEvidences.length) return;

            window.existingEvidences.forEach(ev => {
                const container = document.getElementById(
                    ev.type === "photo"
                        ? "preview-images-existing"
                        : "preview-videos-existing"
                );

                const div = document.createElement("div");
                div.className = "relative group preview-evidence";
                div.dataset.id = ev.id;
                div.dataset.path = ev.path;
                div.dataset.type = ev.type === "photo" ? "image" : "video";

                div.innerHTML = `
                    <div class="h-[150px] rounded overflow-hidden border">
                        ${
                            ev.type === "photo"
                                ? `<img src="${ev.path}" class="w-full h-full object-cover">`
                                : `<video src="${ev.path}" muted controls class="w-full h-full object-cover"></video>`
                        }
                    </div>
                    <button type="button"
                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-5 h-5 text-xs delete-evidence"
                        data-id="${ev.id}">×</button>
                `;

                container.appendChild(div);
            });

            togglePreview();
        }


        /* ================= HELPERS ================= */
        function togglePreview() {
            const hasExisting =
                document.querySelectorAll(".preview-evidence").length > 0;

            const hasNew = photos.length > 0 || videos.length > 0;

            document
                .getElementById("media-preview")
                .classList.toggle("hidden", !hasExisting && !hasNew);
        }


        /* ================= EVENTS ================= */
        document.addEventListener("DOMContentLoaded", () => {
            renderExistingEvidence();

            const start = document.getElementById("start");
            const finish = document.getElementById("finish");
            const dateInput = document.getElementById("date");

            function updateFinishTime(startTime) {
                if (!startTime) return;
                const [h, m] = startTime.split(":").map(Number);
                const d = new Date();
                d.setHours(h + 2, m, 0);
                const val = `${String(d.getHours()).padStart(2,"0")}:${String(d.getMinutes()).padStart(2,"0")}`;
                finish.value = val;
                finish.min = val;
            }

            if (start.value) updateFinishTime(start.value);
            start.addEventListener("change", e => updateFinishTime(e.target.value));

            dateInput.addEventListener("change", () => {
                const selected = new Date(dateInput.value);
                const limit = new Date();
                limit.setMonth(limit.getMonth() - 1);
                if (selected < limit) {
                    alert("Date cannot be more than 1 month ago.");
                    dateInput.value = "";
                }
            });
        });

        /* ================= FILE INPUT ================= */
        document.addEventListener("change", e => {
            if (e.target.id === "photo-input") {
                photos.push(...e.target.files);
                refreshPreview("photo");
                syncInput("photo");
            }

            if (e.target.id === "video-input") {
                videos.push(...e.target.files);
                refreshPreview("video");
                syncInput("video");
            }
        });

        /* ================= REMOVE ================= */
        document.addEventListener("click", e => {
            const remove = e.target.closest(".remove-file");
            const del = e.target.closest(".delete-evidence");

            if (remove) {
                const { type, index } = remove.dataset;
                (type === "photo" ? photos : videos).splice(index, 1);
                refreshPreview(type);
                syncInput(type);
            }

            if (del && confirm("Delete this evidence?")) {
                fetch(`/overwork/evidence/${del.dataset.id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        del.closest(".preview-evidence").remove();
                        togglePreview();
                    } else {
                        alert(data.message || "Failed to delete");
                    }
                });
            }
        });

        /* ================= PASTE ================= */
        document.addEventListener("paste", e => {
            for (const item of e.clipboardData.items) {
                if (item.type.startsWith("image")) {
                    photos.push(item.getAsFile());
                    refreshPreview("photo");
                    syncInput("photo");
                    break;
                }
                if (item.type.startsWith("video")) {
                    videos.push(item.getAsFile());
                    refreshPreview("video");
                    syncInput("video");
                    break;
                }
            }
        });
        </script>

@endsection