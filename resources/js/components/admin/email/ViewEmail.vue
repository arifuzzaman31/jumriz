<template>
  <div class="animated fadeInRight">
    <div class="mail-box-header">
      <h2>Compose mail</h2>
    </div>
    
    <div class="col-md-12" v-if="validationError" style="margin-top: 20px">
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

    <form @submit.prevent="send">
      <div class="mail-box">
        <div class="mail-body">
          <div class="form-group row">
            <label class="col-sm-2 col-form-label">To (Users):</label>
            <div class="col-sm-8">
              <multiselect 
                v-model="users.selected_email" 
                tag-placeholder="Add this as new email" 
                placeholder="Chose or write email" 
                label="email" 
                track-by="id" 
                :options="emails" 
                :multiple="true" 
                :taggable="true" 
                @tag="addUserTag"
              ></multiselect>
            </div>
            <div class="col-sm-2 m-auto">
              <label> 
                <input type="checkbox" v-model="users.all_user" class="icheckbox_square-green" @change="loadUser"> All User 
              </label>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">To (Subscribers):</label>
            <div class="col-sm-8">
              <multiselect 
                v-model="users.selected_subscriber" 
                tag-placeholder="Add this as new email" 
                placeholder="Chose or write email" 
                label="email" 
                track-by="id" 
                :options="subscriberEmails" 
                :multiple="true" 
                :taggable="true" 
                @tag="addSubscriberTag"
              ></multiselect>
            </div>
            <div class="col-sm-2 m-auto">
              <label> 
                <input type="checkbox" v-model="users.all_subscriber" class="icheckbox_square-green" @change="loadSubscriber"> All Subscriber 
              </label>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-2 col-form-label">Subject:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" v-model="users.subject">
            </div>
          </div>
        </div>
        
        <div class="col-sm-12">
          <!-- Vue 3 Quill Editor Replacement -->
          <QuillEditor 
            v-model:content="users.text_body" 
            contentType="html" 
            theme="snow" 
          />
        </div>

        <div class="mail-body text-right tooltip-demo">
          <button 
            class="btn btn-sm btn-primary" 
            title="Send" 
            type="submit"
            :disabled="isSending"
          >
            <i v-if="isSending" class="fa fa-spinner fa-spin"></i>
            {{ buttonName }}
          </button>
        </div>
        <div class="clearfix"></div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { base_url } from '../../../vue-assets'
import { useMixin } from '../../../mixin'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'

const { successMessage, validationError: showValidationError } = useMixin()

// --- State ---
const isSending = ref(false)
const buttonName = ref('Send')
const validationError = ref(null)
const emails = ref([])
const subscriberEmails = ref([])

const users = reactive({
  selected_email: [],
  selected_subscriber: [],
  all_user: false,
  all_subscriber: false,
  subject: '',
  text_body: ''
})

// --- Methods ---
const emailList = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/get-email`)
    if (!users.all_user) {
      emails.value = response.data.user
    }
    if (!users.all_subscriber) {
      subscriberEmails.value = response.data.subscriber
    }
  } catch (error) {
    console.error('Error fetching email lists:', error)
  }
}

const loadSubscriber = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/get-subscriber-email`)
    users.selected_subscriber = users.all_subscriber ? response.data : []
  } catch (error) {
    console.error('Error loading subscribers:', error)
  }
}

const loadUser = async () => {
  try {
    const response = await axios.get(`${base_url}admin/setting/get-user-email`)
    users.selected_email = users.all_user ? response.data : []
  } catch (error) {
    console.error('Error loading users:', error)
  }
}

// ✅ FIXED: Separated tag functions so subscribers don't accidentally get added to users
const addUserTag = (newTag) => {
  const tag = {
    email: newTag,
    id: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000)
  }
  emails.value.push(tag)
  users.selected_email.push(tag)
}

const addSubscriberTag = (newTag) => {
  const tag = {
    email: newTag,
    id: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000)
  }
  subscriberEmails.value.push(tag)
  users.selected_subscriber.push(tag)
}

const send = async () => {
  isSending.value = true
  buttonName.value = 'Sending...'

  try {
    const response = await axios.post(`${base_url}admin/setting/send/email`, users)
    resetForm()
    successMessage(response.data)
  } catch (error) {
    if (error.response?.status === 422) {
      validationError.value = error.response.data.errors
      showValidationError()
    } else {
      successMessage(error)
    }
  } finally {
    isSending.value = false
    buttonName.value = 'Send'
  }
}

// ✅ FIXED: Now properly resets ALL form fields, including checkboxes
const resetForm = () => {
  Object.assign(users, {
    selected_email: [],
    selected_subscriber: [],
    all_user: false,
    all_subscriber: false,
    subject: '',
    text_body: ''
  })
  validationError.value = null
}

// --- Lifecycle ---
onMounted(() => {
  emailList()
})
</script>