<template>
    <div id="modal-form" class="modal fade">
        <div class="modal-dialog modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="m-t-none m-b">Add Campaign</h3>
                    <button type="button" class="btn btn-default text-right" data-dismiss="modal">X</button>
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
                                    <label>Offer Title *</label> 
                                    <input type="text" v-model="form.campaign_title" class="form-control" placeholder="Campaign Title" required />
                                </div>
                            </div>						

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Offer Image</label> <br>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <span class="btn btn-block btn-primary btn-file">
                                            <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Image</span>
                                            <span class="fileinput-exists">Change Image</span>
                                            <input type="file" @change="onImageChange" />
                                        </span>
                                        <img style="height: 80px;" v-if="bannerPreview" :src="bannerPreview">
                                    </div>
                                </div>
                            </div>							

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Meta Image</label> <br>
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <span class="btn btn-block btn-primary btn-file">
                                            <span class="fileinput-new"><i class="fa fa-camera"></i> Choose Image</span>
                                            <span class="fileinput-exists">Change Image</span>
                                            <input type="file" @change="onMetaImageChange" />
                                        </span>
                                        <img style="height: 80px;" v-if="metaImagePreview" :src="metaImagePreview">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Offer Status *</label> 
                                    <select class="form-control" v-model="form.status" required>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>										
                        </div>	

                        <div class="row">
                            <div class="col-md-12">
                                <multiselect 
                                    v-model="form.product" 
                                    label="product_name" 
                                    track-by="id" 
                                    placeholder="Search for Product" 
                                    open-direction="bottom" 
                                    :options="products" 
                                    :multiple="true" 
                                    :searchable="true" 
                                    :loading="isLoading" 
                                    :internal-search="false" 
                                    :clear-on-select="false" 
                                    :close-on-select="false" 
                                    :options-limit="300" 
                                    :limit="15" 
                                    :limit-text="limitText" 
                                    :max-height="600" 
                                    :show-no-results="false" 
                                    :hide-selected="true" 
                                    @search-change="asyncFind"
                                >
                                    <template #noResult>
                                        <span>No products found.</span>
                                    </template>
                                </multiselect>
                            </div>
                        </div>

                        <!-- Discount Table -->
                        <div class="row" style="margin-top: 20px;">	
                            <div class="col-md-12">
                                <div class="table-responsive" v-if="form.product.length">
                                    <table class="table table-bordered table-condensed table-striped">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Product</th>
                                                <th>Base Price</th>
                                                <th>Discount</th>
                                                <th>Discount Type</th>
                                                <th>Total Discount</th>
                                                <th>Discount Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(value, index) in form.product" :key="index">
                                                <td><img style="height: 60px;" v-lazy="value.feature_image"></td>
                                                <td>{{ value.product_name }}</td>
                                                <td>{{ value.selling_price }}</td>
                                                <td>
                                                    <input v-model.number="value.discount" class="form-control" type="number" min="0" />
                                                </td>
                                                <td>
                                                    <select class="form-control" v-model.number="value.discount_type">
                                                        <option value="1">Amount</option>
                                                        <option value="2">%</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    {{ calculateDiscount(value.discount_type, value.discount, value.selling_price) }}
                                                </td>   
                                                <td>
                                                    {{ value.selling_price - calculateDiscount(value.discount_type, value.discount, value.selling_price) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>							
                            </div>		
                        </div>		

                        <!-- Buttons -->
                        <div class="row" style="margin-top: 20px;">								
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary" :disabled="isSaving">{{ buttonName }}</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../../vue-assets';
import { useMixin } from '../../../../mixin';
import Multiselect from 'vue-multiselect';
import 'vue-multiselect/dist/vue-multiselect.css';

// ✅ State
const isSaving = ref(false);
const buttonName = ref("Save");
const validationError = ref(null);
const isLoading = ref(false);

const products = ref([]);

// ✅ File refs and preview refs
const bannerFile = ref(null);
const bannerPreview = ref('');
const metaImageFile = ref(null);
const metaImagePreview = ref('');

const getDefaultForm = () => ({
    campaign_title: '',  
    // banner and meta_image are now handled as separate files
    status: 1,
    product: [], 
});

const form = reactive(getDefaultForm());

// ✅ Methods
const calculateDiscount = (type, discount, mainAmount) => {
    const safeDiscount = parseFloat(discount) || 0;
    const safeMainAmount = parseFloat(mainAmount) || 0;
    
    if (String(type) === "2") {
        return ((safeDiscount / 100) * safeMainAmount).toFixed(2);
    } else {
        return safeDiscount.toFixed(2);
    }
};

// ✅ Store actual File objects, not base64
const onImageChange = (e) => {
    const files = e.target.files || e.dataTransfer.files;
    if (!files.length) {
        bannerFile.value = null;
        if (bannerPreview.value) {
            URL.revokeObjectURL(bannerPreview.value);
            bannerPreview.value = '';
        }
        return;
    }
    const file = files[0];
    bannerFile.value = file;
    if (bannerPreview.value) URL.revokeObjectURL(bannerPreview.value);
    bannerPreview.value = URL.createObjectURL(file);
};

const onMetaImageChange = (e) => {
    const files = e.target.files || e.dataTransfer.files;
    if (!files.length) {
        metaImageFile.value = null;
        if (metaImagePreview.value) {
            URL.revokeObjectURL(metaImagePreview.value);
            metaImagePreview.value = '';
        }
        return;
    }
    const file = files[0];
    metaImageFile.value = file;
    if (metaImagePreview.value) URL.revokeObjectURL(metaImagePreview.value);
    metaImagePreview.value = URL.createObjectURL(file);
};

const limitText = (count) => {
    return `and ${count} other products`;
};

const asyncFind = (query) => {
    if(query.length < 3) return;
    isLoading.value = true;
    axios.get(`${base_url}admin/offer/product-list/search?keyword=${query}`)
        .then(response => {
            if (response.data.status === 'not-found') {
                products.value = []; 
            } else {
                products.value = response.data.data;
            }
        })
        .catch(error => {
            console.error('Error searching products:', error);
            products.value = [];
        })
        .finally(() => {
            isLoading.value = false;
        });
};

const save = async () => {
    isSaving.value = true;
    buttonName.value = "Saving...";

    // ✅ Build FormData for file upload
    const formData = new FormData();
    formData.append('campaign_title', form.campaign_title);
    formData.append('status', form.status);

    // Append banner file if selected
    if (bannerFile.value) {
        formData.append('banner', bannerFile.value);
    }
    // Append meta image if selected
    if (metaImageFile.value) {
        formData.append('meta_image', metaImageFile.value);
    }

    // Append product data with calculated discount
    form.product.forEach((item, index) => {
        const discountAmount = calculateDiscount(item.discount_type, item.discount, item.selling_price);
        formData.append(`product[${index}][id]`, item.id);
        formData.append(`product[${index}][discount]`, item.discount || 0);
        formData.append(`product[${index}][discount_type]`, item.discount_type || 1);
        formData.append(`product[${index}][discount_amount]`, discountAmount);
    });

    try {
        const response = await axios.post(`${base_url}admin/offer`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        if (response.data.status === 'success') {
            if (typeof $ !== 'undefined') {
                $('#modal-form').modal('hide');
            }
            resetForm();
            Mixin.methods.successMessage(response.data);
            EventBus.$emit('campaign-created');
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

const resetForm = () => {
    Object.assign(form, getDefaultForm());
    products.value = [];
    validationError.value = null;
    isLoading.value = false;

    // ✅ Clear file refs and revoke object URLs
    if (bannerPreview.value) {
        URL.revokeObjectURL(bannerPreview.value);
        bannerPreview.value = '';
    }
    bannerFile.value = null;
    // Clear file input DOM element
    const bannerInput = document.querySelector('#modal-form input[type="file"]');
    if (bannerInput) bannerInput.value = '';

    if (metaImagePreview.value) {
        URL.revokeObjectURL(metaImagePreview.value);
        metaImagePreview.value = '';
    }
    metaImageFile.value = null;
    const metaInput = document.querySelector('#modal-form input[type="file"]:last-of-type');
    if (metaInput) metaInput.value = '';
};

// ✅ Lifecycle Hooks
onMounted(() => {
    // Reset form cleanly when bootstrap modal is fully hidden
    if (typeof $ !== 'undefined') {
        $('#modal-form').on('hidden.bs.modal', resetForm);
    }
});

onBeforeUnmount(() => {
    // Clean up jQuery listener to prevent memory leaks
    if (typeof $ !== 'undefined') {
        $('#modal-form').off('hidden.bs.modal', resetForm);
    }
});
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