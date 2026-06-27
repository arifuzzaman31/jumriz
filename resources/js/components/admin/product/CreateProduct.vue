<template>
  <div>
    <!-- Main Product Modal -->
    <div id="modal-form" class="modal fade">
      <div class="modal-dialog modal-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="m-t-none m-b">Add Product</h3>
            <button class="btn btn-default text-right" data-dismiss="modal">X</button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="save">
              <!-- Validation Errors -->
              <div class="row" v-if="validationError">
                <div class="col-md-12" style="margin-top: 20px">
                  <div class="form-group">
                    <ul>
                      <li class="text-danger" v-for="(error, index) in validationError" :key="index">
                        {{ error[0] }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" v-model="product.product_name" class="form-control" placeholder="Product Name" required />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Product Native Name</label>
                    <input type="text" v-model="product.product_native_name" class="form-control" placeholder="Product Native Name" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Category *</label>
                    <multiselect
                      v-model="product.category"
                      deselect-label=""
                      track-by="id"
                      label="category_name"
                      :searchable="true"
                      open-direction="bottom"
                      placeholder="Choose Category"
                      :options="categories"
                      @update:model-value="getSubCategories"
                    />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sub Category *</label>
                    <multiselect
                      v-model="product.sub_category"
                      deselect-label=""
                      track-by="id"
                      label="sub_category_name"
                      :loading="isCategoryLoading"
                      :searchable="true"
                      open-direction="bottom"
                      placeholder="Choose Sub Category"
                      :options="subCategoriesList"
                      @update:model-value="getSubSubCategories"
                    />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sub Sub Category</label>
                    <multiselect
                      v-model="product.sub_sub_category"
                      deselect-label=""
                      track-by="id"
                      label="sub_sub_category_name"
                      :loading="isSubCategoryLoading"
                      :searchable="true"
                      open-direction="bottom"
                      placeholder="Choose Sub Sub Category"
                      :options="subSubCategoriesList"
                      @update:model-value="getBrands"
                    />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Brand</label>
                    <multiselect
                      v-model="product.brand"
                      deselect-label=""
                      track-by="id"
                      label="brand_name"
                      :loading="isBrandLoading"
                      :searchable="true"
                      open-direction="bottom"
                      placeholder="Choose a Brand"
                      :options="brandsList"
                    />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Stock Quantity *</label>
                    <input type="number" v-model="product.quantity" class="form-control" placeholder="Product Quantity" required />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Quantity Unit</label>
                    <input type="text" v-model="product.quantity_unit" class="form-control" placeholder="Ex: 1 pieces, 1/2 Kg" />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Cost Price/Quantity *</label>
                    <input type="text" v-model="product.buying_price" class="form-control" placeholder="Cost Price" required />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Sale Price/Quantity *</label>
                    <input type="text" v-model="product.selling_price" class="form-control" placeholder="Sale Price" required />
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Product Tags *</label>
                    <multiselect
                      v-model="product.product_tag"
                      tag-placeholder="Add this as new tag"
                      placeholder="Search or add a tag"
                      label="keyword_name"
                      track-by="id"
                      :options="tags"
                      :multiple="true"
                      :taggable="true"
                      @tag="addTag"
                    />
                  </div>
                </div>

                <!-- Sizes -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Choose Size</label>
                    <multiselect
                      v-model="product.size"
                      label="name"
                      track-by="id"
                      placeholder="Type to search"
                      open-direction="bottom"
                      :options="sizes"
                      :multiple="true"
                      :searchable="true"
                      :internal-search="true"
                      :clear-on-select="false"
                      :close-on-select="false"
                      :options-limit="300"
                      :limit="6"
                      :max-height="600"
                      :show-no-results="false"
                      :hide-selected="true"
                    >
                      <template #tag="{ option, remove }">
                        <span class="custom__tag">
                          <span>{{ option.name }}</span>
                          <span class="custom__remove" @click="remove(option)">X</span>
                        </span>
                      </template>
                      <template #clear>
                        <div class="multiselect__clear" v-if="product.size.length" @mousedown.prevent.stop="product.size = []"></div>
                      </template>
                      <template #noResult>
                        <span>Oops! No elements found.</span>
                      </template>
                    </multiselect>
                  </div>
                </div>

                <!-- Colors -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Choose Color</label>
                    <span class="float-right" style="cursor:pointer" @click="colorModal">Add Color</span>
                    <multiselect
                      v-model="product.color"
                      label="name"
                      track-by="id"
                      placeholder="Type to search"
                      open-direction="bottom"
                      :options="colors"
                      :multiple="true"
                      :searchable="true"
                      :internal-search="true"
                      :clear-on-select="false"
                      :close-on-select="false"
                      :options-limit="300"
                      :limit="6"
                      :max-height="600"
                      :show-no-results="false"
                      :hide-selected="true"
                    >
                      <template #tag="{ option, remove }">
                        <span class="custom__tag" :style="{ color: '#fff', 'background-color': option.color_code }">
                          <span>{{ option.name }}</span>
                          <span class="custom__remove" @click="remove(option)">❌</span>
                        </span>
                      </template>
                      <template #clear>
                        <div class="multiselect__clear" v-if="product.color.length" @mousedown.prevent.stop="product.color = []"></div>
                      </template>
                      <template #noResult>
                        <span>Oops! No elements found.</span>
                      </template>
                    </multiselect>
                  </div>
                </div>

                <!-- Feature Image -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Main/Feature Image * <small>(600X600)</small></label><br>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                      <span class="btn btn-block btn-primary btn-file">
                        <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Image</span>
                        <span class="fileinput-exists">Change Image</span>
                        <input type="file" @change="onImageChange" />
                      </span>
                      <img style="height: 80px" v-if="featureImagePreview" :src="featureImagePreview" />
                    </div>
                  </div>
                </div>

                <!-- Multiple Images -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Multiple Image <small>(600X600)</small></label><br>
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                      <span class="btn btn-block btn-primary btn-file">
                        <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Images</span>
                        <span class="fileinput-exists">Change Images</span>
                        <input id="attachments" type="file" multiple="multiple" @change="uploadFieldChange" />
                      </span>
                    </div>
                    <div class="attachment-holder animated fadeIn" v-cloak v-for="(attachment, index) in product.attachments" :key="index">
                      <span class="label label-primary">{{ attachment.name + " (" + Number((attachment.size / 1024 / 1024).toFixed(1)) + "MB)" }}</span>
                      <span style="margin-left: 5px;">
                        <button type="button" class="btn btn-xs btn-danger" @click.prevent="removeAttachment(attachment)">Remove</button>
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Quill Editors -->
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Short Description</label>
                    <quill-editor v-model:content="product.short_description" content-type="html" theme="snow" />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Product Description</label>
                    <quill-editor v-model:content="product.description" content-type="html" theme="snow" />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label>Additional Information</label>
                    <quill-editor v-model:content="product.additional_info" content-type="html" theme="snow" />
                  </div>
                </div>

                <div class="col-md-12" style="margin-top: 20px;">
                  <button type="submit" class="btn btn-primary" :disabled="isSaving">
                    {{ buttonName }}
                  </button>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Inline Color Modal -->
    <div id="modal-color" class="modal fade">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="m-t-none m-b">Add Color</h3>
            <button class="btn btn-default text-right" data-dismiss="modal">X</button>
          </div>
          <div class="modal-body">
            <div class="row" v-if="validationError">
              <div class="col-md-12" style="margin-top: 20px">
                <ul>
                  <li class="text-danger" v-for="(error, index) in validationError" :key="index">{{ error[0] }}</li>
                </ul>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Color Name *</label> 
                  <input type="text" v-model="colorForm.name" class="form-control" placeholder="Color Name" />
                </div>
              </div>						
              <div class="col-md-4">
                <div class="form-group">
                  <label>Color *</label> 
                  <input type="color" v-model="colorForm.color_code" class="form-control form-control-color" />
                </div>
              </div>	
              <div class="col-md-4">
                <div class="form-group">
                  <label>Color Code *</label> 
                  <input type="text" v-model="colorForm.color_code" class="form-control" placeholder="#FFFFFF" />
                </div>
              </div>	
            </div>	
            <div class="row" style="margin-top: 20px;">								
              <div class="col-md-12 text-right">
                <button type="button" class="btn btn-primary" @click.prevent="addColor">{{ buttonName }}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount, nextTick } from 'vue';
import { EventBus, base_url } from "../../../vue-assets";
import { useMixin } from "../../../mixin";

// ✅ Vue 3 Quill Editor
import { QuillEditor } from '@vueup/vue-quill'
import 'quill/dist/quill.snow.css';

import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

// ✅ Props
const props = defineProps({
  categories: { type: Array, default: () => [] },
  trial_setting: { type: Object, default: () => ({}) }
});

const getJQuery = () => window.jQuery || window.$;

const showBootstrapModal = (selector) => {
  const jq = getJQuery();
  if (!jq) {
    console.error('jQuery not available');
    return;
  }
  const $el = jq(selector);
  if (!$el.length) {
    console.error('Modal element not found:', selector);
    return;
  }
  if (typeof $el.modal === 'function') {
    $el.modal('show');
  } else {
    console.error('Bootstrap modal plugin not available on jQuery');
  }
};

const hideBootstrapModal = (selector) => {
  const jq = getJQuery();
  if (!jq) return;
  const $el = jq(selector);
  if ($el.length && typeof $el.modal === 'function') {
    $el.modal('hide');
  }
};

// ✅ State
const isSaving = ref(false);
const buttonName = ref("Save");
const validationError = ref(null);

const subCategoriesList = ref([]);
const subSubCategoriesList = ref([]);
const brandsList = ref([]);
const sizes = ref([]);
const colors = ref([]);
const tags = ref([]);

const isCategoryLoading = ref(false);
const isSubCategoryLoading = ref(false);
const isBrandLoading = ref(false);

const formData = ref(new FormData());

const featureImageFile = ref(null);          // stores the actual File
const featureImagePreview = ref('');         // for preview (object URL)

const getDefaultProduct = () => ({
  product_name: "",
  product_native_name: "",
  category: "",
  sub_category: "",
  sub_sub_category: "",
  brand: "",
  quantity: "",
  quantity_unit: "",
  buying_price: "",
  selling_price: "",
  product_tag: [],
  image: "",
  size: [],
  color: [],
  trialable: 0,
  attachments: [],
  description: "",
  short_description: "",
  additional_info: "",
  status: 1,
});

const product = reactive(getDefaultProduct());

const colorForm = reactive({
  from: "createproduct",
  name: "",
  color_code: "",
});

const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files;
  if (!files.length) {
    featureImageFile.value = null;
    featureImagePreview.value = '';
    return;
  }
  const file = files[0];
  
  // ✅ Store the ACTUAL file object (not base64)
  featureImageFile.value = file; 

  // Generate a temporary URL just for the preview on the screen
  if (featureImagePreview.value) {
    URL.revokeObjectURL(featureImagePreview.value);
  }
  featureImagePreview.value = URL.createObjectURL(file);
};

const uploadFieldChange = (e) => {
  let files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  for (let i = files.length - 1; i >= 0; i--) {
    product.attachments.push(files[i]);
  }
  document.getElementById("attachments").value = [];
};

const removeAttachment = (attachment) => {
  product.attachments.splice(product.attachments.indexOf(attachment), 1);
};

const prepareFields = () => {
  formData.value = new FormData(); // Reset completely each time
  
  if (product.attachments.length > 0) {
    for (var i = 0; i < product.attachments.length; i++) {
      formData.value.append("attachments[]", product.attachments[i]);
    }
  }
  
  formData.value.append("product_name", product.product_name);
  formData.value.append("product_native_name", product.product_native_name);
  formData.value.append("category", product.category?.id || "");
  formData.value.append("sub_category", product.sub_category?.id || "");
  formData.value.append("sub_sub_category", product.sub_sub_category?.id || "");
  formData.value.append("brand", product.brand?.id || "");
  formData.value.append("quantity", product.quantity);
  formData.value.append("quantity_unit", product.quantity_unit);
  formData.value.append("buying_price", product.buying_price);
  formData.value.append("selling_price", product.selling_price);
  formData.value.append("description", product.description);
  formData.value.append("short_description", product.short_description);
  formData.value.append("additional_info", product.additional_info);
  formData.value.append("status", product.status);

  if (featureImageFile.value) {
    formData.value.append('image', featureImageFile.value);
  }

  if (product.product_tag.length > 0) {
    product.product_tag.forEach((tag, i) => formData.value.append(`product_tag[${i}]`, tag.keyword_name));
  }
  if (product.size.length > 0) {
    product.size.forEach((siz, i) => formData.value.append(`product_size[${i}]`, siz.id));
  }
  if (product.color.length > 0) {
    product.color.forEach((col, i) => formData.value.append(`product_color[${i}]`, col.id));
  }
};

const save = async () => {
  isSaving.value = true;
  buttonName.value = "Saving...";

  prepareFields();

  try {
    const response = await axios.post(`${base_url}admin/product`, formData.value, {
      headers: { "Content-Type": "multipart/form-data" }
    });

    if (response.data.status === "success") {
      hideBootstrapModal('#modal-form');
      resetForm();
      Mixin.methods.successMessage(response.data);
      EventBus.$emit("product-created");
    } else {
      Mixin.methods.successMessage(response.data);
    }
    buttonName.value = "Save";
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      Mixin.methods.validationError?.();
    } else {
      Mixin.methods.successMessage(err);
    }
    buttonName.value = "Save";
  } finally {
    isSaving.value = false;
  }
};

// Dropdown fetchers
const getSubCategories = () => {
  product.sub_category = ""; product.sub_sub_category = ""; product.brand = ""; product.size = [];
  subCategoriesList.value = []; subSubCategoriesList.value = []; brandsList.value = []; sizes.value = [];
  // console.log(product.category)
  if (product.category?.id) {
    isCategoryLoading.value = true;
    axios.get(`${base_url}admin/get-subcategory/${product.category.id}`).then(res => { subCategoriesList.value = res.data.data; isCategoryLoading.value = false; });
    axios.get(`${base_url}admin/get-sizes/${product.category.id}`).then(res => { sizes.value = res.data; });
  }
};

const getSubSubCategories = () => {
  product.sub_sub_category = ""; product.brand = "";
  subSubCategoriesList.value = []; brandsList.value = [];
  if (product.sub_category?.id) {
    isSubCategoryLoading.value = true;
    axios.get(`${base_url}admin/get-sub-subcategory/${product.sub_category.id}`).then(res => { subSubCategoriesList.value = res.data; isSubCategoryLoading.value = false; });
  }
};

const getBrands = () => {
  product.brand = ""; brandsList.value = [];
  if (product.sub_sub_category?.id) {
    isBrandLoading.value = true;
    axios.get(`${base_url}admin/get-brand/${product.sub_sub_category.id}`).then(res => { brandsList.value = res.data; isBrandLoading.value = false; });
  }
};

const getColors = () => {
  axios.get(`${base_url}admin/get-color`).then(res => { colors.value = res.data; });
};

const addTag = (newTag) => {
  const tag = { keyword_name: newTag, id: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000) };
  tags.value.push(tag);
  product.product_tag.push(tag);
};

const colorModal = () => { showBootstrapModal('#modal-color'); };

const addColor = async () => {
  const code = colorForm.color_code.trim();
  if (!code.startsWith('#') || code.length !== 7) {
    Mixin.methods.successMessage({ status: "error", message: "Enter a valid Color Code!" });
    return;
  }
  try {
    const response = await axios.post(`${base_url}admin/product-color`, colorForm);
    hideBootstrapModal('#modal-color');
    colors.value.push(response.data);
    product.color.push(response.data);
    resetColorForm();
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      Mixin.methods.validationError?.();
    } else {
      Mixin.methods.successMessage(err);
    }
  }
};

const resetColorForm = () => { colorForm.name = ""; colorForm.color_code = ""; };

const resetForm = () => {
  Object.assign(product, getDefaultProduct());
  validationError.value = null;
  subCategoriesList.value = [];
  subSubCategoriesList.value = [];
  brandsList.value = [];
  tags.value = [];
  
  // Clear image preview
  if (featureImagePreview.value) {
    URL.revokeObjectURL(featureImagePreview.value);
    featureImagePreview.value = '';
  }
  featureImageFile.value = null;
  
  // Clear file input elements
  const imageInput = document.querySelector('#modal-form input[type="file"]');
  if (imageInput) imageInput.value = '';
  const attachmentInput = document.getElementById('attachments');
  if (attachmentInput) attachmentInput.value = '';
};

const handleCreateProduct = (payload) => {
  console.log('handleCreateProduct called - event received', { payload });
  resetForm();
  nextTick(() => {
    showBootstrapModal('#modal-form');
  });
};

// ✅ Lifecycle Hooks
onMounted(() => {
  console.log('CreateProduct mounted - registering create-product listener');
  getColors();

  // Listen for the event from ViewProduct
  EventBus.$on('create-product', handleCreateProduct);
  console.log('create-product listener registered');

  // Reset form cleanly when bootstrap modal is fully hidden
  const jq = getJQuery();
  if (jq) {
    jq('#modal-form').on('hidden.bs.modal', resetForm);
  }
});

onBeforeUnmount(() => {
  EventBus.$off('create-product', handleCreateProduct);

  // Clean up jQuery listener to prevent memory leaks
  const jq = getJQuery();
  if (jq) {
    jq('#modal-form').off('hidden.bs.modal', resetForm);
  }
});

// onBeforeUnmount(() => {
//   const modalEl = document.getElementById('modal-form');
//   if (modalEl) modalEl.removeEventListener('show.bs.modal', resetForm);
// });

// onBeforeUnmount(() => {
//   // ✅ Clean up event listener to prevent memory leaks
//   if (typeof $ !== 'undefined') {
//     $('#modal-form').off('hidden.bs.modal', resetForm);
//   }
//   EventBus.$off('create-product', handleCreateProduct);
// });
</script>

<style scoped>
.modal-custom { max-width: 90% !important; }
@media screen and (max-width: 573px) {
  .modal-custom { max-width: 100% !important; background-color: #000 !important; }
}
.custom__tag {
  display: inline-block;
  padding: 3px 8px;
  background: #1ab394;
  color: white;
  margin-right: 5px;
  border-radius: 3px;
  font-size: 12px;
}
.custom__remove { cursor: pointer; margin-left: 5px; }
</style>