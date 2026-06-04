<template>
	<div class="search" id="searchBox">
		<form>
			<div class="input-box">
				<input
          :value="keyword"
          @input="evt=>keyword=evt.target.value"
          name="key" 
          class="single-input" 
          placeholder="Search for product..."
           @keyup="searchProduct()" type="text">
			</div>
		</form>
	</div>
</template>

<script>
  import {EventBus} from  '../../../vue-assets';
	import Mixin from  '../../../mixin';
  export default
  {
   data()
   {
     return {
      keyword : '',
     }
   },

    methods: {
    	searchProduct()
    	{
       
      EventBus.$emit('scrol-to-result',this.keyword);
    	}
    }
  }
</script>