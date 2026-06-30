<template>
  <div ref="modalRef" id="modal-showpage" class="modal fade">
    <div class="modal-dialog modal-custom">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="m-t-none m-b">Show Page</h3>
          <button class="btn btn-default text-right" data-dismiss="modal">
            X
          </button>
        </div>
        
        <div class="modal-body">
          <div class="wrapper wrapper-content animated fadeInRight article">
            <div class="row justify-content-md-center">
              <div class="col-lg-10">
                <div class="ibox">
                  <div class="ibox-content">
                    <div class="text-center" style="margin: 10px 0 40px 0px">
                      <span class="text-muted">
                        <i class="fa fa-clock-o"></i> {{ dateToString(page.created_at) }}
                      </span>
                      <h1>
                        {{ page.title }}
                      </h1>
                    </div>
                    <!-- Fixed: Changed <p> to <div> to prevent block-element nesting errors from WYSIWYG HTML -->
                    <div v-html="page.description" style="text-align: justify"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onUnmounted } from 'vue'
import { emitter } from '../../../../vue-assets'
import { useMixin } from '../../../../mixin'

// ✅ Get dateToString since Vue 3 removed filters
const { dateToString } = useMixin()

// --- State ---
const modalRef = ref(null)

const page = reactive({
  title: '',
  description: '',
  created_at: '',
})

// --- Methods ---
const openModal = (value) => {
  Object.assign(page, value)
  if (modalRef.value) {
    $(modalRef.value).modal('show')
  }
}

// --- Lifecycle ---
onMounted(() => {
  emitter.on('show-page', openModal)
})

onUnmounted(() => {
  emitter.off('show-page', openModal)
})
</script>

<style scoped>
.modal-custom {
  max-width: 90% !important;
}

@media screen and (max-width: 573px) {
  .modal-custom {
    max-width: 100% !important;
    background-color: #000 !important;
  }
}
</style>