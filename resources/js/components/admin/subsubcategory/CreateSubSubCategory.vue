<template>
    <div id="modal-form" class="modal fade" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header text-right">
                    <button class="btn btn-default" data-dismiss="modal">X</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Form Section -->
                        <div class="col-sm-8 b-r">
                            <h3 class="m-t-none m-b">Add Sub Sub Category</h3>

                            <form @submit.prevent="save" role="form">
                                
                                <!-- Category Select -->
                                <div class="form-group">
                                    <label>Choose Category *</label> 
                                    <select v-model="subcategory.category" class="form-control" @change="getSubCategories" required>
                                        <option value="">Please Choose a Category</option>
                                        <option v-for="value in categories" :key="value.id" :value="value.id">
                                            {{ value.category_name }}
                                        </option>
                                    </select>
                                </div>	

                                <!-- Sub Category Select -->
                                <div class="form-group">
                                    <label>Choose Sub Category *</label> 
                                    <select v-model="subcategory.sub_category" class="form-control" required>
                                        <option value="">Please Choose a Sub Category</option>
                                        <option v-for="value in subCategoriesList" :key="value.id" :value="value.id">
                                            {{ value.sub_category_name }}
                                        </option>
                                    </select>
                                </div>								

                                <!-- Name -->
                                <div class="form-group">
                                    <label>Sub Sub Category Name *</label> 
                                    <input v-model="subcategory.name" type="text" placeholder="Name" class="form-control" required>
                                </div>									

                                <!-- Native Name -->
                                <div class="form-group">
                                    <label>Native Name</label> 
                                    <input v-model="subcategory.native_name" type="text" placeholder="Native Sub Category Name" class="form-control">
                                </div>								

                                <!-- Brands Multiselect -->
                                <div class="form-group">
                                    <label>Choose Brand</label> 
                                    <multiselect 
                                        v-model="subcategory.selected_brand" 
                                        label="brand_name" 
                                    _track-by="id" 
                                        placeholder="Type to search" 
                                        open-direction="bottom" 
                                        :options="brands" 
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
                                        <!-- ✅ Vue 3 Slot Syntax for Tags -->
                                        <template #tag="{ option, remove }">
                                            <span class="custom__tag">
                                                <span>{{ option.brand_name }}</span>
                                                <span class="custom__remove" @click="remove(option)"> ❌</span>
                                            </span>
                                        </template>
                                        
                                        <!-- ✅ Vue 3 Slot Syntax for Clear -->
                                        <template #clear>
                                            <div 
                                                class="multiselect__clear" 
                                                v-if="subcategory.selected_brand.length" 
                                                @mousedown.prevent.stop="clearBrands"
                                            ></div>
                                        </template>
                                        
                                        <!-- ✅ Vue 3 Slot Syntax for No Result -->
                                        <template #noResult>
                                            <span>Oops! No elements found. Consider changing the search query.</span>
                                        </template>
                                    </multiselect>
                                </div>									

                                <!-- Icon Upload -->
                                <div class="form-group">
                                    <label>Icon (128x128) *</label> <br>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <span class="btn btn-block btn-primary btn-file">
                                            <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Icon</span>
                                            <span class="fileinput-exists">Change Icon</span>
                                            <input type="file" @change="onImageChange" />
                                        </span>
                                    </div> 
                                </div>	

                                <!-- Status -->
                                <div class="form-group">
                                    <label>Status *</label> 
                                    <select name="status" class="form-control" v-model="subcategory.status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>

                                <!-- Submit -->
                                <div style="margin-bottom: 20px;">
                                    <button 
                                        class="btn btn-lg btn-primary float-right" 
                                        type="submit" 
                                        :disabled="isLoading"
                                    >
                                        <strong>{{ buttonName }}</strong>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Image Preview Section -->
                        <div class="col-sm-4">
                            <h4>Photo Preview</h4>
                            <p class="text-center" v-if="subcategory.image">
                                <img class="img-responsive img-fluid" :src="subcategory.image">
                            </p>
                            <p class="text-center text-muted" v-else>
                                <i class="fa fa-image fa-3x"></i>
                                <br>
                                <small>No image selected</small>
                            </p>
                        </div>

                        <!-- Validation Errors -->
                        <div class="col-md-12" v-if="validationError" style="margin-top: 20px">
                            <div class="form-group">
                                <div>
                                    <ul>
                                        <li class="text-danger" v-for="(error, index) in validationError" :key="index">
                                            {{ error[0] }}
                                        </li>
                                    </ul>
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
import { ref, reactive } from 'vue';
import { EventBus, base_url } from '../../../vue-assets';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';
import Mixin from '../../../mixin';

// ✅ Props
const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    brands: {
        type: Array,
        default: () => [],
    }
});

// ✅ Reactive State
const subCategoriesList = ref([]);
const isLoading = ref(false);
const buttonName = ref("Save");
const validationError = ref(null);

const subcategory = reactive({
    category: '',  
    sub_category: '',  
    name: '',  
    native_name: '',  
    image: '',  
    status: 1,
    selected_brand: [],  
});

// ✅ Default State for Resetting
const getDefaultState = () => ({
    category: '',  
    sub_category: '',  
    name: '',  
    native_name: '',  
    image: '',  
    status: 1,
    selected_brand: [],  
});

// ✅ Methods
const onImageChange = (e) => {
    const files = e.target.files || e.dataTransfer.files;
    if (!files.length) return;
    createImage(files[0]);
};

const createImage = (file) => {
    const reader = new FileReader();
    reader.onload = (e) => {
        subcategory.image = e.target.result;
    };
    reader.readAsDataURL(file);
};

const getSubCategories = () => {
    // Reset sub category when parent category changes
    subcategory.sub_category = '';
    subCategoriesList.value = [];

    if (subcategory.category) {
        axios.get(`${base_url}admin/get-subcategory/${subcategory.category}`)
            .then(response => {
                subCategoriesList.value = response.data;
            })
            .catch(error => {
                console.error('Error fetching subcategories:', error);
            });
    }
};

const clearBrands = () => {
    subcategory.selected_brand = [];
};

const hideModal = () => {
    if (typeof $ !== 'undefined') {
        $('#modal-form').modal('hide');
    }
};

const resetForm = () => {
    Object.assign(subcategory, getDefaultState());
    validationError.value = null;
    subCategoriesList.value = [];
};

const save = async () => {
    isLoading.value = true;
    buttonName.value = "Saving...";

    try {
        const response = await axios.post(`${base_url}admin/sub-sub-category`, subcategory);

        if (response.data.status === 'success') {
            hideModal();
            resetForm();
            Mixin.methods.successMessage(response.data);
            
            // ✅ Emit event to refresh list
            EventBus.$emit('sub-sub-category-created');
            
            buttonName.value = "Save";
        } else {
            Mixin.methods.successMessage(response.data);	
            buttonName.value = "Save";
        }
    } catch (err) {
        if (err.response?.status === 422) {
            validationError.value = err.response.data.errors;
            Mixin.methods.validationError?.();
            buttonName.value = "Save";
        } else {
            Mixin.methods.successMessage(err);
            buttonName.value = "Save";
        }
    } finally {
        isLoading.value = false;
    }
};
</script>

<style scoped>
/* Optional: Keep your custom tag styles if you had them */
.custom__tag {
  display: inline-block;
  padding: 3px 8px;
  background: #1ab394;
  color: white;
  margin-right: 5px;
  border-radius: 3px;
  font-size: 12px;
}
.custom__remove {
  cursor: pointer;
  margin-left: 5px;
}
</style>