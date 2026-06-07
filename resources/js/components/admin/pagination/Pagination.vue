<template>
	
	<div class="row">
				<div class="text-center col-md-12" v-if="pageData.last_page > 1">
					<ul class="pagination">
						<li :class="[ ((pageData.current_page == 1) ? 'disabled' : '') ]">
							<a
								:href="'?page='+pageData.current_page"
								@click.prevent="pageClicked(pageData.current_page-1)"
								aria-label="Previous"
								v-if="pageData.current_page != 1"
								>
								<span aria-hidden="true">«</span>
							</a>
							<a v-else>
								<span aria-hidden="true">«</span>
							</a>
						</li>
						<li
							v-for="pageNo in range(paginateLoop, numberOfPage)"
							:class="[ ((pageData.current_page == pageNo) ? 'active' : '') ]"
							>
							<a :href="'?page='+pageNo" @click.prevent="pageClicked(pageNo)">{{ pageNo }}</a>
						</li>
						<li :class="[ ((pageData.current_page == pageData.last_page) ? 'disabled' : '') ]">
							<a
								:href="'?page='+pageData.current_page"
								@click.prevent="pageClicked(pageData.current_page+1)"
								aria-label="Next"
								v-if="pageData.current_page != pageData.last_page"
								>
								<span aria-hidden="true">»</span>
							</a>
							<a v-else>
								<span aria-hidden="true">»</span>
							</a>
						</li>
					</ul>
				</div>
			</div>

</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    pageData: {
        type: Object,
        required: true
    }
});

const emit = defineEmits(['page-clicked']);

const range = (start, count) => {
    return Array.apply(0, Array(count)).map((element, index) => index + start);
};

const pageClicked = (page) => {
    emit('page-clicked', page);
};

const paginateLoop = computed(() => {
    if (props.pageData.last_page > 11) {
        if (props.pageData.last_page - 5 <= props.pageData.current_page) {
            return props.pageData.last_page - 10;
        }
        if (props.pageData.current_page > 6) {
            return props.pageData.current_page - 5;
        }
    }
    return 1;
});

const numberOfPage = computed(() => {
    return props.pageData.last_page < 11 ? props.pageData.last_page : 11;
});
</script>

<style scoped>
	.disabled {
		cursor: not-allowed;
	}

	.active a{

		background-color: #E3106E !important;
		color: #fff !important;
	}
</style>