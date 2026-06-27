<template>
    <div id="modal-discount" class="modal fade">
        <div class="modal-dialog modal-custom">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="m-t-none m-b">Add Discount</h3>
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

                        <!-- Discount Table -->
                        <div class="row" style="margin-top: 20px;">	
                            <div class="col-md-12">
                                <div class="table-responsive">
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
                                                <th>Discount Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><img style="height: 60px;" v-lazy="featureImage" /></td>
                                                <td>{{ productName }}</td>
                                                <td>{{ sellingPrice }}</td>
                                                <td>
                                                    <input 
                                                        v-model.number="discform.discount" 
                                                        class="form-control" 
                                                        type="number" 
                                                        min="0"
                                                    />
                                                </td>
                                                <td>
                                                    <select class="form-control" v-model.number="discform.discount_type">
                                                        <option value="1">Amount</option>
                                                        <option value="2">%</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <!-- ✅ Removed hacky hidden input, using clean computed property instead -->
                                                    {{ calculatedDiscount }}
                                                </td>   
                                                <td>
                                                    {{ finalPrice }}
                                                </td>
                                                <td>
                                                    <select class="form-control" v-model.number="discform.discount_status">
                                                        <option :value="1">ON</option>
                                                        <option :value="0">OFF</option>
                                                    </select>
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
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue';
import { EventBus, base_url } from '../../../vue-assets';
import { useMixin } from '../../../mixin';

// ✅ State
const isSaving = ref(false);
const buttonName = ref("Save");
const validationError = ref(null);

const featureImage = ref('');
const productName = ref('');
const sellingPrice = ref('');

const discform = reactive({
    id: '',  
    discount: 0, 
    discount_type: 1, 
    discount_status: null,
});

// ✅ Computed Properties (Replaces the bad hidden input hack)
const calculatedDiscount = computed(() => {
    if (discform.discount_type === 2) {
        return parseFloat(((discform.discount / 100) * sellingPrice.value)).toFixed(2);
    } else {
        return parseFloat(discform.discount || 0).toFixed(2);
    }
});

const finalPrice = computed(() => {
    const price = parseFloat(sellingPrice.value) - parseFloat(calculatedDiscount.value);
    return Math.max(0, price).toFixed(2); // Prevent negative prices
});

// ✅ Methods
const getProduct = (id) => {
    axios.get(`${base_url}admin/product/${id}/discount`)
        .then(response => {
            const data = response.data.data;
            featureImage.value = data.feature_image;
            productName.value = data.product_name;
            sellingPrice.value = data.selling_price;
            
            discform.id = data.id;
            discform.discount = data.discount;
            discform.discount_type = data.discount_type;
            discform.discount_status = data.discount_status;
        })
        .catch(error => {
            console.error('Error fetching product discount:', error);
        });
};

const save = async () => {
    isSaving.value = true;
    buttonName.value = "Saving...";
    
    // Append the calculated amount to the form data before sending
    const payload = {
        ...discform,
        discount_amount: calculatedDiscount.value
    };

    try {
        const response = await axios.post(`${base_url}admin/set-discount`, payload);
        
        if (typeof $ !== 'undefined') {
            $('#modal-discount').modal('hide');
        }
        
        Mixin.methods.successMessage(response.data);
        EventBus.$emit('product-created'); // Refresh product list
        resetForm();
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
    discform.id = '';
    discform.discount = 0;
    discform.discount_type = 1;
    discform.discount_status = null;
    
    featureImage.value = '';
    productName.value = '';
    sellingPrice.value = '';
    validationError.value = null;
};

// ✅ Event Handlers
const handleDiscountProduct = (id) => {
    getProduct(id);
    if (typeof $ !== 'undefined') {
        $('#modal-discount').modal('show');
    }
};

// ✅ Lifecycle Hooks
onMounted(() => {
    EventBus.$on('discount-product', handleDiscountProduct);
});

onBeforeUnmount(() => {
    EventBus.$off('discount-product', handleDiscountProduct);
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