@extends('layouts.app')

@section('content')
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
            New Overwork Request
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

                {{-- Evidence Preview --}}
                @if (isset($evidence) && count($evidence) > 0)
                    <div class="p-2 py-5">
                        {{-- Images --}}
                        <div class="flex flex-wrap mb-4">
                            @foreach ($evidence as $e)
                                @php
                                    $ext = strtolower(pathinfo($e->path, PATHINFO_EXTENSION));
                                @endphp

                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                    <div class="relative group mr-2 mb-2 rounded overflow-hidden" style="width: 100px; height: 100px">
                                        <img src="{{ asset('storage/' . $e->path) }}" alt="" class="w-full h-full object-cover rounded" />

                                        @if(isset($overwork))
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button
                                                    type="button"
                                                    class="text-white hover:text-gray-300 preview-evidence"
                                                    data-path="{{ asset('storage/' . $e->path) }}"
                                                    data-type="image"
                                                    data-id="{{ $e->id }}"
                                                    title="Preview"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="text-white hover:text-gray-300 delete-evidence"
                                                    data-id="{{ $e->id }}"
                                                    title="Delete"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        {{-- Videos --}}
                        <div class="flex flex-wrap">
                            @foreach ($evidence as $e)
                                @php
                                    $ext = strtolower(pathinfo($e->path, PATHINFO_EXTENSION));
                                @endphp

                                @if (in_array($ext, ['mp4', 'mov', 'avi']))
                                    <div class="relative group mr-2 mb-2 rounded overflow-hidden" style="width: 100px; height: 100px">
                                        <video autoplay loop muted playsinline class="w-full h-full object-cover rounded">
                                            <source src="{{ asset('storage/' . $e->path) }}" type="video/mp4" />
                                        </video>

                                        @if(isset($overwork))
                                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button
                                                    type="button"
                                                    class="text-white hover:text-gray-300 preview-evidence"
                                                    data-path="{{ asset('storage/' . $e->path) }}"
                                                    data-type="video"
                                                    data-id="{{ $e->id }}"
                                                    title="Preview"
                                                >
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button
                                                    type="button"
                                                    class="text-white hover:text-gray-300 delete-evidence"
                                                    data-id="{{ $e->id }}"
                                                    title="Delete"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
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
                <p class="text-gray-500 mt-6">Please upload a photo or video evidence for validation<span class="text-red-500">*</span></p>
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

                {{-- Media Preview --}}
                <div id="media-preview" class="mt-4 hidden">
                    <h4 class="font-bold text-md mb-2">Selected Files Preview:</h4>
                    <div id="preview-images" class="flex flex-wrap gap-2 mb-2 w-[150px]"></div>
                    <div id="preview-videos" class="flex flex-wrap gap-2 w-[150px]"></div>
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
    <script>
        function syncPhotoInput() {
            const dt = new DataTransfer();
            photos.forEach(p => dt.items.add(p));
            document.getElementById("photo-input").files = dt.files;
        }

        // showPicker for custom trigger
        ['date'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', () => el.showPicker());
        });

        // Auto-update finish time +2h
        function updateFinishTime(startTimeStr) {
            if (!startTimeStr) return;
            const [h, m] = startTimeStr.split(':').map(Number);
            const finish = new Date();
            finish.setHours(h + 2, m, 0);
            const timeStr = `${String(finish.getHours()).padStart(2, '0')}:${String(finish.getMinutes()).padStart(2, '0')}`;
            const finishInput = document.getElementById('finish');
            finishInput.value = timeStr;
            finishInput.min = timeStr;
            finishInput.disabled = false;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const start = document.getElementById('start');
            if (start?.value) updateFinishTime(start.value);
            start?.addEventListener('change', e => updateFinishTime(e.target.value));
        });

        // === Evidence Viewer Logic ===
        let evidences = [], currentIndex = 0;

        function collectEvidences() {
            evidences = [...document.querySelectorAll('.preview-evidence')].map(btn => ({
                path: btn.dataset.path,
                type: btn.dataset.type,
                id: btn.dataset.id,
            }));
        }

        function showEvidence(index) {
            const e = evidences[index];
            const body = document.getElementById('evidence-viewer-body');
            body.innerHTML = e.type === 'image'
                ? `<img src="${e.path}" class="max-w-full max-h-[80vh] rounded shadow-lg" />`
                : `<video src="${e.path}" controls autoplay class="max-w-full max-h-[80vh] rounded shadow-lg"></video>`;
            document.getElementById('prev-evidence').style.display = index > 0 ? 'block' : 'none';
            document.getElementById('next-evidence').style.display = index < evidences.length - 1 ? 'block' : 'none';
        }

        document.addEventListener('click', e => {
            const preview = e.target.closest('.preview-evidence');
            const del = e.target.closest('.delete-evidence');

            if (preview) {
                e.preventDefault();
                collectEvidences();
                currentIndex = evidences.findIndex(ev => ev.path === preview.dataset.path);
                showEvidence(currentIndex);
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'evidence-viewer-modal' }));
            }

            if (del && confirm('Are you sure you want to delete this evidence?')) {
                const id = del.dataset.id;
                fetch(`/overwork/evidence/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) del.closest('.relative').remove();
                    else alert('Failed to delete: ' + data.message);
                })
                .catch(() => alert('An error occurred while deleting.'));
            }
        });

        document.getElementById('prev-evidence')?.addEventListener('click', () => currentIndex > 0 && showEvidence(--currentIndex));
        document.getElementById('next-evidence')?.addEventListener('click', () => currentIndex < evidences.length - 1 && showEvidence(++currentIndex));

        // === Media Preview ===
        let photos = [], videos = [];

        function refreshPreview(type) {
            const container = document.getElementById(type === 'photo' ? 'preview-images' : 'preview-videos');
            container.innerHTML = '';
            const files = type === 'photo' ? photos : videos;

            files.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <div class="h-[150px] rounded overflow-hidden border-2 border-gray-300">
                            ${type === 'photo'
                                ? `<img src="${e.target.result}" class="w-full h-full object-cover">`
                                : `<video src="${e.target.result}" muted controls autoplay loop class="w-full h-full object-cover"></video>`}
                        </div>
                        <button type="button"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 remove-file"
                            data-type="${type}" data-index="${i}">×</button>`;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            document.getElementById('media-preview').classList.toggle('hidden', photos.length === 0 && videos.length === 0);
        }

        document.addEventListener('change', e => {
            if (e.target.id === 'photo-input') {
                photos = [...photos, ...e.target.files];
                refreshPreview('photo');
                syncPhotoInput(); // <— FIX
            }

            if (e.target.id === 'video-input') {
                videos = [...videos, ...e.target.files];
                refreshPreview('video');
            }
        });

        document.addEventListener('click', e => {
            const rm = e.target.closest('.remove-file');
            if (rm) {
                const type = rm.dataset.type;
                const idx = +rm.dataset.index;
                (type === 'photo' ? photos : videos).splice(idx, 1);
                refreshPreview(type);
            }
        });


        document.addEventListener("DOMContentLoaded", function() {
            const dateInput = document.getElementById("date");

            dateInput.addEventListener("change", function() {
                const selectedDate = new Date(this.value);
                const today = new Date();

                const oneMonthAgo = new Date();
                oneMonthAgo.setMonth(today.getMonth() - 1);

                if (selectedDate < oneMonthAgo) {
                    alert("The selected date must not be more than one month ago.");
                    this.value = "";
                }
            });
        });

        // === PASTE IMAGE TO PHOTO INPUT ===
        document.addEventListener("paste", function (e) {
            const items = e.clipboardData.items;
            if (!items) return;

            for (let item of items) {
                if (item.type.indexOf("image") === 0) {
                    const file = item.getAsFile();
                    photos.push(file);
                    refreshPreview("photo");
                    syncPhotoInput(); // <— FIX
                    break;
                }
            }
        });
    </script>
@endsection