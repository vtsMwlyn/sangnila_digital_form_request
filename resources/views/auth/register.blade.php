@push('styles')
<style>
    .ts-wrapper {
        border: none;
    }

    .ts-wrapper .ts-control {
        padding-inline: 11px;
        padding-block: 13px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }

    .ts-wrapper.focus .ts-control {
        border-color: #6366f1;
        border-width: 2px;
    }

    /* .ts-wrapper .ts-control .item,
    .ts-wrapper .ts-control div {
        font-size: 16px;
    } */


    .ts-wrapper {
        border: none;
    }

    .ts-wrapper .ts-control {
        padding-inline: 11px;
        padding-block: 13px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
    }

    .ts-wrapper.focus .ts-control {
        border-color: #6366f1;
        border-width: 2px;
    }

    /* ✅ RESPONSIVE FORM (MOBILE & TABLET) */
    @media (max-width: 1024px) {
        table {
            display: block;
            width: 100%;
        }

        table tbody,
        table tr,
        table td {
            display: block;
            width: 100%;
        }

        td {
            padding-right: 0 !important;
            padding-left: 0 !important;
        }

        .p-6 {
            padding: 1rem;
        }

        .text-right {
            text-align: center;
        }

        .text-right button {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush
 @extends('layouts.tables') @section('content')
{{-- @if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif --}}

<form method="POST" action="{{ route('register') }}" autocomplete="off">
    @csrf

    <div
        class="p-6 bg-[#F0F3F8] rounded-2xl shadow-lg max-w-full mx-auto text-black"
        :class="$el.closest('[x-data]')?.__x.$data.sidebarOpen ? 'max-w-full' : 'max-w-6xl'"
    >
        <!-- Title -->
        <h2 class="text-2xl font-bold text-center text-[#042E66] mb-6">
            Add Employee Account
        </h2>

        <!-- Subtitle -->
        <h3 class="font-semibold text-[#042E66] mb-4">Employee Information</h3>

        <div
            :class="$el.closest('[x-data]')?.__x.$data.sidebarOpen ? 'overflow-x-auto' : ''"
            class="w-full"
        >
            <table class="w-full border-collapse min-w-[800px]">
                <tbody>
                    <tr class="">
                        <!-- Left Column -->
                        <td
                            class="pr-8 w-1/2 align-top"
                            :class="$el.closest('[x-data]')?.__x.$data.sidebarOpen ? 'pr-4' : 'pr-8'"
                        >
                            <!-- Name -->
                            <div class="mb-4">
                                <label
                                    for="name"
                                    class="font-semibold text-sm block mb-1"
                                    >Name</label
                                >
                                <x-text-input
                                    placeholder="New User's Fullname"
                                    id="name"
                                    type="text"
                                    name="name"
                                    :value="old('name')"
                                    required
                                    autofocus
                                    class="w-full rounded border px-3 py-2"
                                />
                                <x-input-error
                                    :messages="$errors->get('name')"
                                    class="mt-1 text-red-600"
                                />
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <label
                                    for="email"
                                    class="font-semibold text-sm block mb-1"
                                    >Email</label
                                >
                                <x-text-input
                                    placeholder="New User's Email"
                                    id="email"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    class="w-full rounded border border-black px-3 py-2"
                                />
                                <x-input-error
                                    :messages="$errors->get('email')"
                                    class="mt-1 text-red-600"
                                />
                            </div>

                            <!-- Password -->
                            <div class="mb-4">
                                <label for="password" class="font-semibold text-sm block mb-1">Password</label>

                                <div class="relative">
                                    <x-text-input
                                        id="password"
                                        type="password"
                                        name="password"
                                        placeholder="Create Password"
                                        required
                                        class="w-full rounded border border-black px-3 py-2 pr-10"
                                    />

                                    <button
                                    type="button"
                                    id="togglePassword"
                                    aria-label="Show password"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800 focus:outline-none"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </button>
                                </div>

                                <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-600" />
                            </div>


                            {{--  --}}

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label
                                    for="password_confirmation"
                                    class="font-semibold text-sm block mb-1"
                                    >Confirm Password</label
                                >
                                <div class="relative">
                                <x-text-input
                                    placeholder="confirm the password"
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    class="w-full rounded border border-black px-3 py-2 pr-10"
                                />

                                <button
                                type="button"
                                id="togglePasswordConfirm"
                                aria-label="Show password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-gray-800 focus:outline-none"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                </button>
                                </div>
                                <x-input-error
                                    :messages="$errors->get('password_confirmation')"
                                    class="mt-1"
                                />
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-4">
                                <label
                                    for="phone"
                                    class="font-semibold text-sm block mb-1"
                                    >Phone Number</label
                                >
                                <x-text-input
                                    placeholder="New User's Phone Number"
                                    id="phone"
                                    type="text"
                                    name="phone_number"
                                    :value="old('phone_number')"
                                    required
                                    class="w-full rounded border border-black px-3 py-2"
                                />
                                <x-input-error
                                    :messages="$errors->get('phone')"
                                    class="mt-1 text-red-600"
                                />
                            </div>

                            {{-- status employee --}}
                            <div class="mb-4">
                                <label
                                    for="status"
                                    class="font-semibold text-sm block mb-1"
                                    >Status Employee</label
                                >
                                <select
                                id="status"
                                name="status"
                                required
                                placeholder="Select New User Status"
                                class="w-full rounded border border-gray-300 px-3 py-3 shadow-sm text-sm"
                                >
                                <option disabled hidden selected >Select New User Status</option>
                                <option value="Employee">Employee</option>
                                <option value="intern">Intern</option>
                                </select>
                                <x-input-error
                                    :messages="$errors->get('status')"
                                    class="mt-1 text-red-600"
                                />
                            </div>
                        </td>

                        <!-- Right Column -->
                        <td
                            class="align-top w-1/2"
                            :class="$el.closest('[x-data]')?.__x.$data.sidebarOpen ? 'pl-4' : ''"
                        >

                        <div class="mb-4 w-full">
                            <label
                                for="overwork_allowance"
                                class="font-semibold text-sm block mb-1"
                                >Leave Balance</label
                            >
                            <div class="flex flex-col md:flex-row gap-4">
                            <x-text-input
                                placeholder="Enter Leave Balance in Days"
                                id="Leave_Balance"
                                type="text"
                                name="Leave_Balance_Day"
                                :value="old('Leave_Balance')"
                                required
                                autofocus
                                class="w-full rounded border px-3 py-2"
                            />
                            <x-text-input
                            placeholder="Enter Leave Balance in hours"
                            id="Leave_Balance"
                            type="text"
                            name="Leave_Balance_Hour"
                            :value="old('Leave_Balance')"

                            autofocus
                            class="w-full rounded border px-3 py-2"
                            />
                            </div>
                            <x-input-error
                                :messages="$errors->get('Leave_Balance')"
                                class="mt-1 text-red-600"
                            />
                        </div>

                        <div class="mb-4 w-full">
                            <label for="Total_Overwork" class="font-semibold text-sm block mb-1">
                                Total Overwork
                            </label>

                            <div class="flex flex-col md:flex-row gap-4">
                                <x-text-input
                                    placeholder="Enter Total Overwork in Days"
                                    id="Total_Overwork_Day"
                                    type="text"
                                    name="Total_Overwork_Day"
                                    :value="old('Total_Overwork_Day')"
                                    required
                                    class="w-full md:w-1/2 rounded border px-3 py-2"
                                />

                                <x-text-input
                                    placeholder="Enter Total Overwork in Hours"
                                    id="Total_Overwork_Hour"
                                    type="text"
                                    name="Total_Overwork_Hour"
                                    :value="old('Total_Overwork_Hour')"

                                    class="w-full md:w-1/2 rounded border px-3 py-2"
                                />
                            </div>

                            <x-input-error :messages="$errors->get('Total_Overwork')" class="mt-1 text-red-600" />
                        </div>

                        <div class="mb-4">
                            <label for="positionSelect" class="font-semibold text-sm block mb-1">Position</label>

                            <select
                              id="positionSelect"
                              name="position"
                              required
                              onchange="handleSelectChange('position')"
                              placeholder= "Select Position"
                              class="w-full rounded border border-gray-300 px-3 py-3 shadow-sm text-sm"
                            >
                              <option disabled hidden selected>Select Position</option>
                              <option value="Admin">Admin</option>
                              <option value="CEO/Director">CEO/Director</option>
                              <option value="Human Resources">Human Resources</option>
                              <option value="3D Artist">3D Artist</option>
                              <option value="Finance and Accountant">Finance and Accountant</option>
                              <option value="Concept Artist">Concept Artist</option>
                              <option value="Animator">Animator</option>
                              <option value="Graphic Designer">Graphic Designer</option>
                              <option value="Sales and Marketing">Sales and Marketing</option>
                              <option value="other">Other</option>
                            </select>

                            <input
                              type="text"
                              id="positionInput"
                              name="position_other"
                              placeholder="Enter custom position"
                              class="hidden w-full rounded border border-gray-300 px-3 py-3 shadow-sm text-sm"
                            />

                            <x-input-error :messages="$errors->get('position')" class="mt-1 text-red-600"/>
                        </div>

                          <!-- DEPARTMENT -->
                        <div class="mb-4">
                            <label for="departmentSelect" class="font-semibold text-sm block mb-1">Department</label>

                            <select
                              id="departmentSelect"
                              name="department"
                              required
                              onchange="handleSelectChange('department')"
                              placeholder= "Select Department"
                              class="w-full rounded border border-gray-300 px-3 py-3 shadow-sm text-sm"
                            >
                              <option disabled hidden selected> Select Department</option>
                              <option value="Admin">Admin</option>
                              <option value="Executive">Executive</option>
                              <option value="Human Resources">Human Resources</option>
                              <option value="Finance">Finance</option>
                              <option value="3D">3D</option>
                              <option value="Concept Art">Concept Art</option>
                              <option value="Animation">Animation</option>
                              <option value="Graphic Design">Graphic Design</option>
                              <option value="Marketing">Marketing</option>
                              <option value="other">Other</option>
                            </select>

                            <input
                              type="text"
                              id="departmentInput"
                              name="department_other"
                              placeholder="Enter custom department"
                              class="hidden w-full rounded border border-gray-300 px-3 py-3 shadow-sm"
                            />

                            <x-input-error :messages="$errors->get('department')" class="mt-1 text-red-600"/>
                        </div>

                            <!-- Role -->
                        <div class="mb-4">
                            <label for="Role" class="font-semibold text-sm block mb-1">Role</label>
                            <select
                            id="role"
                            name="role"
                            required
                            placeholder="Select New User Role"
                            class="w-full rounded border border-gray-300 px-3 py-3 shadow-sm text-sm"
                            >
                            <option disabled hidden selected >Select New User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            </select>
                            <x-input-error
                                :messages="$errors->get('role')"
                                class="mt-1 text-red-600"
                            />
                        </div>
                    </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Submit Button -->
        <div class="text-right mt-6">
            <x-primary-button class="inline-flex items-center gap-2">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-white"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Add Account
            </x-primary-button>
        </div>
    </div>
</form>
{{-- <script>
(function () {
  function initToggle(btnId, inputId, labelShow, labelHide) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    if (!btn || !input) return;

    const eyeSVG = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/></svg>';
    const eyeOffSVG = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/><path stroke-linecap="round" stroke-linejoin="round" d="M10.477 10.477A3 3 0 0113.523 13.523"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.88 6.88C8.155 6.47 9.571 6.29 11 6.29c4.477 0 8.268 2.943 9.542 7-0.34 1.082-0.9 2.07-1.642 2.923M3.17 8.53A9.953 9.953 0 002.458 12c1.274 4.057 5.065 7 9.542 7 1.429 0 2.845-.18 4.121-.59"/></svg>';

    btn.innerHTML = eyeSVG;

    btn.addEventListener('click', e => {
      e.preventDefault();
      const hidden = input.type === 'password';
      input.type = hidden ? 'text' : 'password';
      btn.innerHTML = hidden ? eyeOffSVG : eyeSVG;
      btn.setAttribute('aria-label', hidden ? labelHide : labelShow);
      input.focus({ preventScroll: true });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      initToggle('togglePassword', 'password', 'Show password', 'Hide password');
      initToggle('togglePasswordConfirm', 'password_confirmation', 'Show confirm password', 'Hide confirm password');
    });
  } else {
    initToggle('togglePassword', 'password', 'Show password', 'Hide password');
    initToggle('togglePasswordConfirm', 'password_confirmation', 'Show confirm password', 'Hide confirm password');
  }
})();
</script> --}}

@endsection
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        new TomSelect('#role', {
            create: false,
        });
    });
</script> --}}

<script>
    function toggleOther(field) {
        const select = document.getElementById(field);
        const input = document.getElementById(field + '_other');

        if (select.value === 'other') {
            input.classList.remove('hidden');
            input.name = field;
            select.name = '';
        } else {
            input.classList.add('hidden');
            select.name = field;
            input.name = field + '_other';
        }
    }
    </script>

<script>
    function handleSelectChange(field) {
      const selectEl = document.getElementById(field + "Select");
      const inputEl = document.getElementById(field + "Input");

      if (selectEl.value === "other") {
        selectEl.classList.add("hidden");
        inputEl.classList.remove("hidden");
        inputEl.focus();
      } else {
        inputEl.classList.add("hidden");
        selectEl.classList.remove("hidden");
        inputEl.value = "";
      }
    }
  </script>

<script>
    // ======================
    // PASSWORD TOGGLE (UNIVERSAL)
    // ======================
    function initPasswordToggle(buttonId, inputId) {
        const btn = document.getElementById(buttonId);
        const input = document.getElementById(inputId);
        if (!btn || !input) return;

        const eye = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10.5 10.5A3 3 0 0113.5 13.5"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.8 6.8C8.1 6.4 9.6 6.2 11 6.2c4.5 0 8.3 3 9.6 7
                    -0.3 1.1-0.9 2.1-1.6 2.9M3.2 8.5A10 10 0 002.5 12c1.3 4.1 5.1 7 9.5 7
                    1.4 0 2.9-.2 4.2-.6"/>
            </svg>`;

        const eyeOff = `
             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943
                    9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
            </svg>`;


        btn.innerHTML = eye;

        btn.addEventListener("click", (e) => {
            e.preventDefault();
            const isHidden = input.type === "password";
            input.type = isHidden ? "text" : "password";
            btn.innerHTML = isHidden ? eyeOff : eye;
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        initPasswordToggle("togglePassword", "password");
        initPasswordToggle("togglePasswordConfirm", "password_confirmation");
    });


    // ======================
    // SELECT → OTHER HANDLER
    // ======================
    function handleSelectChange(field) {
        const select = document.getElementById(field + "Select");
        const input = document.getElementById(field + "Input");

        if (select.value === "other") {
            select.classList.add("hidden");
            input.classList.remove("hidden");
            input.focus();
        } else {
            input.classList.add("hidden");
            select.classList.remove("hidden");
            input.value = "";
        }
    }
    </script>

