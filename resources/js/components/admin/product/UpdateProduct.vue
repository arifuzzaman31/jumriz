<template>
  <div>
    <div id="modal-update" class="modal fade">
      <div class="modal-dialog modal-custom">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="m-t-none m-b">Update Product</h3>
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
                    <label>Current Quantity *</label>
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
                      <!-- ✅ Vue 3 Slot Syntax -->
                      <template #tag="{ option, remove }">
                        <span class="custom__tag">
                          <span>{{ option.name }}</span>
                          <span class="custom__remove" @click="remove(option)">❌</span>
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
                    <label>Choose Color <span @click="colorModalUpdate" class="float-right" style="cursor:pointer">Add Color</span></label>
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
                      <!-- ✅ Vue 3 Slot Syntax -->
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

                <div class="col-md-3" v-if="trial_setting?.status == 1">
                  <div class="form-group">
                    <label>Trialable</label>
                    <select class="form-control" v-model="product.trialable">
                      <option value="1">ON</option>
                      <option value="0">OFF</option>
                    </select>
                  </div>
                </div>

                <!-- Feature Image -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Main/Feature Image (600x600) *</label> <br />
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                      <span class="btn btn-block btn-primary btn-file">
                        <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Image</span>
                        <span class="fileinput-exists">Change Image</span>
                        <input type="file" @change="onImageChange" />
                      </span>
                      <img style="height: 100px" v-if="product.image" :src="product.image" />
                      <img style="height: 100px" v-else v-lazy="product.view_image" />
                    </div>
                  </div>
                </div>

                <!-- Multiple Images -->
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Multiple Image (600x600) *</label> <br />
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                      <span class="btn btn-block btn-primary btn-file">
                        <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Images</span>
                        <span class="fileinput-exists">Change Images</span>
                        <input id="attachments" type="file" multiple="multiple" @change="uploadFieldChange" />
                      </span>
                    </div>

                    <!-- Existing Multiple Images -->
                    <div class="img-frame" v-if="multipleImage.length > 0" v-for="images in multipleImage" :key="images.id" style="margin-top:5px; display:inline-block; margin-right:5px;">
                      <img v-lazy="images.image" style="max-height: 100px; position: relative;" />
                      <a @click.prevent="deleteImage(images.id)" href="#" class="text-danger"><i class="fa fa-trash"></i></a>
                    </div>

                    <!-- New Attachments -->
                    <div class="attachment-holder animated fadeIn" v-cloak v-for="(attachment, index) in product.attachments" :key="index">
                      <span class="label label-primary">{{ attachment.name + " (" + Number((attachment.size / 1024 / 1024).toFixed(1)) + "MB)" }}</span>
                      <span style="margin-left: 5px;">
                        <button type="button" class="btn btn-xs btn-danger" @click.prevent="removeAttachment(attachment)">Remove</button>
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Quill Editors (Replaced vue2-editor) -->
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

    <!-- (Optional) Color Modal - Cleaned up syntax just in case you uncomment it -->
    <!-- <div id="modal-color-update" class="modal fade">
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
                <button type="button" class="btn btn-primary" @click.prevent="colorAddInUpdate">Add Color</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from "../../../vue-assets";
import Mixin from "../../../mixin";

// ✅ Vue 3 Quill Editor
import { QuillEditor } from '@vueup/vue-quill';
import 'quill/dist/quill.snow.css';

import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

// ✅ Props
const props = defineProps({
  trial_setting: { type: Object, default: () => ({}) }
});

// ✅ State
const isSaving = ref(false);
const buttonName = ref("Update");
const validationError = ref(null);

const categories = ref([]);
const subCategoriesList = ref([]);
const subSubCategoriesList = ref([]);
const brandsList = ref([]);
const sizes = ref([]);
const colors = ref([]);
const tags = ref([]);
const multipleImage = ref([]);

const isCategoryLoading = ref(false);
const isSubCategoryLoading = ref(false);
const isBrandLoading = ref(false);

const formData = ref(new FormData());

const getDefaultProduct = () => ({
  id: "",
  product_name: "",
  product_native_name: "",
  category: "",
  sub_category: "",
  sub_sub_category: "",
  brand: "",
  size: [],
  color: [],
  trialable: "",
  quantity: "",
  quantity_unit: "",
  buying_price: "",
  selling_price: "",
  product_tag: [],
  image: "",
  view_image: "",
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

// ✅ Methods
const onImageChange = (e) => {
  const files = e.target.files || e.dataTransfer.files;
  if (!files.length) return;
  const reader = new FileReader();
  reader.onload = (e) => { product.image = e.target.result; };
  reader.readAsDataURL(files[0]);
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
  formData.value = new FormData(); // Reset each time
  
  if (product.attachments.length > 0) {
    for (var i = 0; i < product.attachments.length; i++) {
      formData.value.append("attachments[]", product.attachments[i]);
    }
  }
  formData.value.append("id", product.id);
  formData.value.append("product_name", product.product_name);
  formData.value.append("product_native_name", product.product_native_name);
  formData.value.append("category", product.category?.id || "");
  formData.value.append("sub_category", product.sub_category?.id || "");
  formData.value.append("sub_sub_category", product.sub_sub_category?.id || "");
  formData.value.append("brand", product.brand?.id || "");
  formData.value.append("quantity", product.quantity);
  formData.value.append("trialable", product.trialable);
  formData.value.append("quantity_unit", product.quantity_unit);
  formData.value.append("buying_price", product.buying_price);
  formData.value.append("selling_price", product.selling_price);
  formData.value.append("description", product.description);
  formData.value.append("short_description", product.short_description);
  formData.value.append("additional_info", product.additional_info);
  formData.value.append("image", product.image);
  
  if (product.product_tag.length > 0) {
    product.product_tag.forEach((tag, i) => formData.value.append(`product_tag[${i}]`, tag.keyword_name));
  }
  if (product.size.length > 0) {
    product.size.forEach((siz, i) => formData.value.append(`product_size[${i}]`, siz.id));
  }
  if (product.color.length > 0) {
    product.color.forEach((col, i) => formData.value.append(`product_color[${i}]`, col.id));
  }
  
  // ❌ REMOVED: formData.value.append('product_tag[]', product.product_tag); 
  // (It was sending [object Object] to Laravel. The forEach loop above handles it correctly).
  
  formData.value.append("status", product.status);
};

const save = async () => {
  isSaving.value = true;
  buttonName.value = "Updating...";

  prepareFields();

  try {
    const response = await axios.post(`${base_url}admin/product/update/${product.id}`, formData.value, {
      headers: { "Content-Type": "multipart/form-data" }
    });

    if (response.data.status === "success") {
      if (typeof $ !== 'undefined') $('#modal-update').modal("hide");
      Mixin.methods.successMessage(response.data);
      EventBus.$emit("product-created");
      resetForm();
    } else {
      Mixin.methods.successMessage(response.data);
    }
    buttonName.value = "Update";
  } catch (err) {
    if (err.response?.status === 422) {
      validationError.value = err.response.data.errors;
      Mixin.methods.validationError?.();
    } else {
      Mixin.methods.successMessage(err);
    }
    buttonName.value = "Update";
  } finally {
    isSaving.value = false;
  }
};

// Dropdown Fetchers
const getSubCategories = () => {
  product.sub_category = ""; product.brand = ""; product.size = "";
  subCategoriesList.value = []; subSubCategoriesList.value = []; brandsList.value = []; sizes.value = [];
  if (product.category?.id) {
    isCategoryLoading.value = true;
    axios.get(`${base_url}admin/get-subcategory/${product.category.id}`).then(res => { subCategoriesList.value = res.data; isCategoryLoading.value = false; });
    getSizeByCategory();
  }
};

const getSizeByCategory = () => {
  product.size = ""; sizes.value = [];
  if (product.category?.id) {
    isCategoryLoading.value = true;
    axios.get(`${base_url}admin/get-sizes/${product.category.id}`).then(res => { sizes.value = res.data; isCategoryLoading.value = false; });
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

const getProduct = (id) => {
  axios.get(`${base_url}admin/product/${id}/edit`).then((response) => {
    categories.value = response.data.categories;
    subCategoriesList.value = response.data.sub_categories;
    subSubCategoriesList.value = response.data.sub_sub_categories;
    brandsList.value = response.data.brands;
    tags.value = response.data.product.product_keyword;
    sizes.value = response.data.sizes;
    
    product.size = response.data.product.product_size;
    product.color = response.data.product.product_color;
    product.product_name = response.data.product.product_name;
    product.product_native_name = response.data.product.product_native_name;
    product.category = response.data.product.category;
    product.sub_category = response.data.product.sub_category;
    product.sub_sub_category = response.data.product.sub_sub_category;
    product.brand = response.data.product.brand;
    product.description = response.data.product.product_description;
    product.short_description = response.data.product.short_description;
    product.additional_info = response.data.product.additional_info;
    product.product_tag = response.data.product.product_keyword;
    product.buying_price = response.data.product.buying_price;
    product.selling_price = response.data.product.selling_price;
    product.quantity = response.data.product.current_quantity;
    product.quantity_unit = response.data.product.quantity_unit;
    product.view_image = response.data.product.feature_image;
    multipleImage.value = response.data.product.multiple_image;
  });
};

const addTag = (newTag) => {
  const tag = { keyword_name: newTag, id: newTag.substring(0, 2) + Math.floor(Math.random() * 10000000) };
  tags.value.push(tag);
  product.product_tag.push(tag);
};

const colorModalUpdate = () => { if (typeof $ !== 'undefined') $("#modal-color-update").modal("show"); };

const colorAddInUpdate = async () => {
  const code = colorForm.color_code.trim();
  if (!code.startsWith('#') || code.length !== 7) {
    Mixin.methods.successMessage({ status: "error", message: "Enter a valid Color Code!" });
    return;
  }
  try {
    const response = await axios.post(`${base_url}admin/product-color`, colorForm);
    if (typeof $ !== 'undefined') $("#modal-color-update").modal("hide");
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
  categories.value = [];
  subCategoriesList.value = [];
  subSubCategoriesList.value = [];
  brandsList.value = [];
  tags.value = [];
  multipleImage.value = [];
  
  // ✅ Polished: Clear the actual DOM input so it doesn't show ghost file names
  const attachmentInput = document.getElementById("attachments");
  if (attachmentInput) attachmentInput.value = "";
};

const deleteImage = (image_id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "The picture will be removed!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      axios.get(`${base_url}admin/product/image/${image_id}/delete`).then((res) => {
        Mixin.methods.successMessage(res.data);
        getProduct(product.id);
      });
    }
  });
};

// ✅ Event Handlers
const handleUpdateProduct = (id) => {
  if (typeof $ !== 'undefined') $('#modal-update').modal("show");
  product.id = id;
  getProduct(id);
};

// ✅ Lifecycle Hooks
onMounted(() => {
  getColors();
  EventBus.$on("update-product", handleUpdateProduct);
  
  if (typeof $ !== 'undefined') {
    $('#modal-update').on('hidden.bs.modal', resetForm);
  }
});

onBeforeUnmount(() => {
  EventBus.$off("update-product", handleUpdateProduct);
  if (typeof $ !== 'undefined') {
    $('#modal-update').off('hidden.bs.modal', resetForm);
  }
});
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