<template>
  <div class="row">
    <div class="col-md-5 border p-4 mx-auto">
      <h3 class="m-t-none m-b">Update Setting</h3>
      <form @submit.prevent="save" role="form">
        <div class="form-group">
          <label>Mail Driver</label>
          <select class="form-control m-b" name="account" v-model="form.driver">
            <option value="smtp">SMTP</option>
            <option value="mail">Mail</option>
            <option value="sendmail">SendMail</option>
          </select>
        </div>

        <div class="form-group">
          <label>Mail Host</label>
          <input v-model="form.host" type="text" class="form-control" />
        </div>

        <div class="form-group">
          <label>Mail Port</label>
          <input v-model="form.port" type="text" class="form-control" />
        </div>

        <div class="form-group">
          <label>Username</label>
          <input v-model="form.username" type="text" class="form-control" />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input v-model="form.password" type="text" class="form-control" />
        </div>

        <div class="form-group">
          <label>From Email</label>
          <input v-model="form.from_address" type="text" class="form-control" />
        </div>

        <div class="form-group">
          <label>From Name</label>
          <input v-model="form.from_name" type="text" class="form-control" />
        </div>

        <div class="form-group">
          <label>Encryption</label>
          <select class="form-control m-b" v-model="form.encryption">
            <option value="null">NULL</option>
            <option value="ssl">SSL</option>
            <option value="tls">TLS</option>
          </select>
        </div>

        <div class="form-group">
          <label>Mailing</label>
          <div class="switch">
            <div class="onoffswitch">
              <input
                type="checkbox"
                :checked="form.status == 1"
                class="onoffswitch-checkbox"
                id="example2"
                @change="mailStatus"
              />
              <label class="onoffswitch-label" for="example2">
                <span class="onoffswitch-inner"></span>
                <span class="onoffswitch-switch"></span>
              </label>
            </div>
          </div>
        </div>

        <div style="margin-bottom: 20px">
          <button
            class="btn btn-lg btn-primary float-right"
            type="submit"
            :disabled="isSubmitting"
          >
            <strong>{{ buttonName }}</strong>
          </button>
        </div>
      </form>
    </div>

    <div class="col-md-5 mx-auto">
      <div class="card">
        <div class="card-header">
          <div class="card-title"><h3>Instuction</h3></div>
        </div>
        <div class="card-body">
          <ul>
            <li>
              Make sure you select IMAP server to send email. The mailing driver
              you'd like to use. By default, this is set to SMTP, but you can
              also change it to use PHPs mail feature or Sendmail instead.
            </li>

            <li>Enter your SMTP server's host address.</li>

            <li>
              Under user information, enter a name and the current email address
              in which you would like to set up.
            </li>

            <li>
              Enter your email address into the username field and your password
              that you have been sent.
            </li>

            <li>
              Turning on 'less secure apps' settings as mailbox user
              <ul>
                <li>Go to your (Google Account).</li>
                <li>On the left navigation panel, click Security.</li>
                <li>
                  On the bottom of the page, in the Less secure app access
                  panel, click Turn on access.
                </li>
                <li>
                  If you don't see this setting, your administrator might have
                  turned off less secure app account access (check the
                  instruction above).
                </li>
                <li>Click the Save button.</li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div
      v-if="validationError"
      class="col-md-12"
      style="margin-top: 20px; margin-bottom: 10px"
    >
      <div class="form-group">
        <ul>
          <li
            v-for="(error, index) in validationError"
            :key="index"
            class="text-danger"
          >
            {{ error[0] }}
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { base_url } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const isSubmitting = ref(false)
const buttonName = ref('Update')
const validationError = ref(null)

const form = reactive({
  driver: '',
  host: '',
  port: '',
  username: '',
  password: '',
  encryption: '',
  from_address: '',
  from_name: '',
  status: '',
})

// --- Methods ---
const getMailData = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/mail/data`)
    Object.assign(form, response.data)
  } catch (error) {
    console.error('Error fetching mail data:', error)
  }
}

const save = async () => {
  isSubmitting.value = true
  buttonName.value = 'Updating...'

  try {
    const response = await axios.post(
      `${base_url}admin/setting/update/mail`,
      form
    )
    successMessage(response.data)
    validationError.value = null
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors
      showValidationError()
    } else {
      successMessage(err)
    }
  } finally {
    isSubmitting.value = false
    buttonName.value = 'Update'
  }
}

const mailStatus = async () => {
  try {
    const response = await axios.post(`${base_url}admin/setting/mail/status`)
    successMessage(response.data)
    form.status = response.data.status
  } catch (error) {
    console.error('Error updating mail status:', error)
  }
}

// --- Lifecycle ---
onMounted(() => {
  getMailData()
})
</script>