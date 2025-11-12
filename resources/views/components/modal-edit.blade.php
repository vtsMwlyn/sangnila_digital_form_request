<x-modal name="edit-modal" maxWidth="2xl">
    <div class="p-4">
        <div class="flex justify-end">
            <button
                @click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'edit-modal' }))"
                class="text-red-500 hover:text-red-300 text-3xl font-bold"
            >
                &times;
            </button>
        </div>

        <div class="p-6 text-gray-800 max-h-[80vh] overflow-y-auto">
            <p class="text-xl font-bold mb-4 text-left">
                Edit Account
            </p>
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="user_id" name="id">

                <div class="mb-4">
                    <label for="name" class="font-semibold text-sm block mb-1">Name</label>
                    <x-text-input id="name" name="name" type="text" class="w-full rounded border px-3 py-2" required />
                </div>

                <div class="mb-4">
                    <label for="email" class="font-semibold text-sm block mb-1">Email</label>
                    <x-text-input id="email" name="email" type="email" class="w-full rounded border px-3 py-2" required />
                </div>

                <div class="mb-4">
                    <label for="phone" class="font-semibold text-sm block mb-1">Phone Number</label>
                    <x-text-input id="phone" name="phone_number" type="text" class="w-full rounded border px-3 py-2" />
                </div>

                <div class="mb-4">
                    <label for="status" class="font-semibold text-sm block mb-1">Department</label>

                    <select
                      id="status"
                      name="status"
                      required
                      placeholder= "Select Status Employee"
                      class="w-full rounded border border-gray-300 px-3 py-3 shadow-sm text-sm"
                    >
                      <option disabled hidden selected> Select Status Employee</option>
                      <option value="contract">Contract</option>
                      <option value="intern">Intern</option>
                    </select>
                </div>

                <div class="flex gap-4">
                <div class="mb-4 w-1/2">
                    <label for="Leave_Balance" class="font-semibold text-sm block mb-1">Leave Balance</label>
                    <x-text-input id="Leave_Balance_Day" name="Leave_Balance_Day" type="text" class="w-full rounded border px-3 py-2"  placeholder="Enter Leave Balance in Day"/>
                </div>

                <div class="mt-6 w-1/2 ">
                    <x-text-input id="Leave_Balance_Hour" name="Leave_Balance_Hour" type="text" class="w-full rounded border px-3 py-2"  placeholder="Enter Leave Balance in Hours" />
                </div>
                </div>

                <div class="flex gap-4">
                <div class="mb-4  w-1/2">
                    <label for="Total_Overwork" class="font-semibold text-sm block mb-1">Total Overwork</label>
                    <x-text-input id="Total_Overwork_Day" name="Total_Overwork_Day" type="text" class="w-full rounded border px-3 py-2"  placeholder="Enter Total Overwork in Day"/>
                </div>

                <div class="mt-6 w-1/2">
                    <x-text-input id="Total_Overwork_Hour" name="Total_Overwork_Hour" type="text" class="w-full rounded border px-3 py-2"  placeholder="Enter Total Overwork in Hours" />
                </div>
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

                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-gradient-to-r from-[#1EB8CD] to-[#2652B8] hover:from-cyan-600 hover:to-blue-800 text-white font-semibold py-2 px-4 rounded-lg transition duration-300"
                    >
                        Save
                    </button>
                </div>
            </form>

        </div>
</x-modal>

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



