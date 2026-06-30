<template>
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox animated fadeInRightBig">
        <div class="ibox-title">
          <h5>Payment List</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <i class="fa fa-wrench"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
              <li><a href="#" class="dropdown-item">Config option 1</a></li>
              <li><a href="#" class="dropdown-item">Config option 2</a></li>
            </ul>
            <a class="close-link">
              <i class="fa fa-times"></i>
            </a>
          </div>
        </div>
        
        <div class="ibox-content">
          <div 
            class="table-responsive" 
            style="margin-top: 15px" 
            v-if="!isLoading"
          >
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Provider</th>
                  <th>Client ID/KEY | PUBLIC_KEY</th>
                  <th>Secret</th>
                  <th>Status</th>
                  <th>Platform</th>
                  <th>Update</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="payment in payments" :key="payment.id">
                  <td>{{ payment.provider }}</td>
                  <td>
                    <span v-if="payment.id === 6" class="text-info">Encryption Key: </span>
                    {{ payment.client_id }}
                  </td>
                  <td>
                    <span v-if="payment.id === 6" class="text-info">SECRET Key: </span>
                    {{ payment.client_secret }} <br>
                    <span v-if="payment.id === 6" class="text-info">
                      Public Key : {{ payment.public_key }}
                    </span>
                  </td>
                  <td>
                    <span v-if="payment.status === 0" class="text-danger">Inactive</span>
                    <span v-else class="text-success">Active</span>
                  </td>
                  <td>
                    <span v-if="payment.live_status === 1" class="text-info">Live</span>
                    <span v-else class="text-warning">SandBox</span>
                  </td>
                  <td>
                    <button 
                      @click="edit(payment)" 
                      class="btn btn-primary" 
                      type="button"
                    >
                      <i class="fa fa-edit" title="Edit"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="col-md-12 text-center" v-else>
            <img :src="`${url}images/loading.gif`" alt="Loading..." />
          </div>
        </div>
      </div>
      
      <div class="ibox">
        <UpdatePayment />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { emitter, base_url } from '../../../../vue-assets'
import UpdatePayment from './UpdatePayment.vue'

// --- State ---
const payments = ref([])
const isLoading = ref(false)
const url = base_url

// --- Methods ---
const getPayments = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(`${base_url}admin/setting/payment-method-list`)
    payments.value = response.data
  } catch (error) {
    console.error('Error fetching payment methods:', error)
  } finally {
    isLoading.value = false
  }
}

const edit = (paymentData) => {
  emitter.emit('update-payment', paymentData)
}

// --- Lifecycle ---
onMounted(() => {
  getPayments()
  emitter.on('payment-created', getPayments)
})

onUnmounted(() => {
  emitter.off('payment-created', getPayments)
})
</script>