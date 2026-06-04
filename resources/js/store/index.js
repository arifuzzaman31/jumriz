import { createStore } from 'vuex'

export default createStore({
    state:{
     
      cart_items : [],
      total_cart_item : 0,
      total_cart_amount : 0,
      isLoading   : false,
      // trial state 
      trial_items : [],
      total_trial_item : 0,
      total_trial_amount : 0,
      isTrialLoading   : false,
    
    },
    getters:{
    
        cart_items(state){
            return state.cart_items
        },
        cart_count(state){
            return state.total_cart_item
        },
        cart_total(state){
        
            return state.total_cart_amount
        },
        cart_loading(state){
            return state.isLoading
        },
    
        productWithId: (state) => (id) => {
            
           
             let ob =  Object.values(state.cart_items);
         
             return ob.find(todo => todo.id == id)
        },
       
    
        // trial commit 
        
        trial_items(state){
            return state.trial_items
        },
        trial_count(state){
            return state.total_trial_item
        },
        trial_total(state){
        
            return state.total_trial_amount
        },
        trial_loading(state){
            return state.isLoading
        },
        
        trialWithId: (state) => (id) => {
            
           
             let ob =  Object.values(state.trial_items);
         
             return ob.find(product => product.id == id)
        }
    },
    actions:{
        getCart(context){
            context.commit('loading_status',true)
            axios.get(base_url+'cart-items')
                .then((response)=>{
                    context.commit('cartItems',response.data.cart_items)
                    context.commit('totalCartItem',response.data.cart_count)
                    context.commit('totalCartAmount',response.data.cart_total)
                    context.commit('loading_status',false);
                })
        },
        
        getTrial(context){
            context.commit('trial_loading',true)
            axios.get(base_url+'trial-items')
                .then((response)=>{
                    context.commit('trialItems',response.data.trial_items)
                    context.commit('totalTrialItem',response.data.trial_count)
                    context.commit('totalTrialAmount',response.data.trial_total)
                    context.commit('trial_loading',false);
                })
        },
    },
    mutations:{
        // cart mutation 
        cartItems(state,data){
            return state.cart_items = data
        },
        totalCartItem(state,payload){
            return state.total_cart_item = payload
        },
        totalCartAmount(state,payload){
            return state.total_cart_amount = payload
        },
        loading_status(state,payload){
            return state.isLoading = payload
        },
        // trial mutation 
        trialItems(state,data){
            return state.trial_items = data
        },
        totalTrialItem(state,payload){
            return state.total_trial_item = payload
        },
        totalTrialAmount(state,payload){
            return state.total_trial_amount = payload
        },
        trial_loading(state,payload){
            return state.isTrialLoading = payload
        }
    
    }
})