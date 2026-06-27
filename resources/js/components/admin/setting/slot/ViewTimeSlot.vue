<template>
  <div>
    <h3 class="m-t-none m-b">Time Slot List</h3>
    <div class="table-responsive" style="max-height: 600px; overflow-y: auto">
      <table class="table">
        <thead>
          <tr>
            <th>Slot Name</th>
            <th>Expired At Same Day</th>
            <th>Status</th>
            <th>Edit</th>
            <th>Delete</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slot in timeSlots" :key="slot.id">
            <td>{{ slot.slot_name }}</td>
            <td>{{ slot.expired_at }}</td>
            <td>
              <span v-if="slot.status === 1" class="badge badge-success">
                Active
              </span>
              <span v-else class="badge badge-danger">Inactive</span>
            </td>
            <td>
              <button
                @click="editSlot(slot)"
                class="btn btn-success"
                type="button"
              >
                Edit
              </button>
            </td>
            <td>
              <button
                @click="deleteSlot(slot.id)"
                class="btn btn-danger"
                type="button"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <EditTimeSlot />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { emitter } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'
import EditTimeSlot from './EditTimeSlot.vue'

const { successMessage } = useMixin()

const timeSlots = ref([])
const isLoading = ref(false)

const getSlot = async () => {
  try {
    isLoading.value = true
    const response = await axios.get(`${base_url}admin/setting/time-slot-list`)
    timeSlots.value = response.data
  } catch (error) {
    console.error('Error fetching time slots:', error)
  } finally {
    isLoading.value = false
  }
}

const editSlot = (slot) => {
  emitter.emit('edit-slot', slot)
}

const deleteSlot = async (id) => {
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!',
  })

  if (result.isConfirmed) {
    try {
      const res = await axios.delete(`${base_url}admin/setting/time-slot/${id}`)
      successMessage(res.data)
      getSlot()
    } catch (error) {
      console.error('Error deleting time slot:', error)
    }
  }
}

onMounted(() => {
  getSlot()
  emitter.on('slot-created', getSlot)
})

onUnmounted(() => {
  emitter.off('slot-created', getSlot)
})
</script>