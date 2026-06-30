<template>
	<div id="modal-order" class="modal fade" >
		<div class="modal-dialog modal-custom">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="ml-3">{{ customer_name }}</h3>
					<button class="btn btn-default text-right" data-dismiss="modal">X</button>
				</div>
				<div class="modal-body">
		            <div class="ibox animated fadeInRightBig">
	                    <div class="table-responsive" style="margin-top: 10px;" v-if="!isLoading">
	                        <table class="table table-bordered">
	                            <thead>
	                            <tr>
	                            	<th>OrderID</th>
	                                <th>Date</th>
	                                <th>Customer</th>
	                                <th>Total Item</th>
	                                <th>Total Amount</th>
	                                <th>Payment Status</th>
	                                <th>Payment Method</th>
	                                <th>Status</th>
	                            </tr>
	                            </thead>
	                            <tbody>
	                            <tr v-for="(value,index) in customer_order" :key="index">
	                            	<td>{{ value.id }}</td>
	                                <td>{{ dateToString(value.order_date) }}</td>
	                                <td>{{ value.user.name }}</td>
	                                <td>{{ value.total_item }}</td>
	                                <td>{{ formatPrice(value.total_amount) }}</td>
	                                <td>
	                                	<span v-if="value.payment_status == 1">Paid</span>
                                		<span v-else>Unpaid</span>
	                                </td>
	                                <td>{{ value.provider.provider }}</td>
	                                <td>
	                                   	<span v-if="value.status == 0">Pending</span>
	                                   	<span v-else-if="value.status == 1">On Process</span>
	                                   	<span v-else-if="value.status == 2">On Delivery</span>
                                		<span v-else>Delivered</span>
	                          		</td>
	                            </tr>
	                            </tbody>
	                        </table>
	                    </div>

	                    <div class="col-md-12 text-center" v-else>
	                        <img :src="url+'images/loading.gif'">
	                    </div>
		            </div>
		            <div class="ibox animated fadeInRightBig">
		               <pagination v-if="customer_order" :pageData="customer_order"></pagination> 
		            </div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import { base_url, emitter } from '../../../vue-assets';
	import { useCommonActions } from '../../../useCommonActions';
	import Pagination from '../pagination/Pagination.vue';

	export default {
		setup() {
			const { dateToString, formatPrice } = useCommonActions();
			return {
				dateToString,
				formatPrice
			}
		},

		components : {
            'pagination' : Pagination,
            // 'show-orderdetails' : showOrderDetails,
        },

		data(){

			return {
				customer_order : [],
				isLoading : false,
				url : base_url,
				customer_name : '',
			}
		},

		mounted()
		{
         emitter.on('customer-orders', ([id, name]) => {
            $('#modal-order').modal('show');
            this.customer_name = name;
            this.getOrder(id);
         });
		},

        unmounted() {
            emitter.off('customer-orders');
        },

		methods : {
            getOrder(id){
             this.isLoading = true
             axios.get(base_url+'admin/customer/'+id+'/show')
                .then(response => {
                	this.customer_order = response.data;
                })
                .finally(() => {
                    this.isLoading = false;
                });
            },
        }
    }
</script>

<style scoped="">
.modal-custom {

	max-width: 90% !important;

}

@media screen and (max-width: 573px)
{


	.modal-custom {

		max-width: 100% !important;
		background-color: #000 !important;
	}



}
</style>