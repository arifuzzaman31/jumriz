import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useCartStore } from './store/index';

const app = createApp({});
const pinia = createPinia();
app.use(pinia);

const Mixin = {
    methods: {
        playCartSound() {
            const audio = new Audio(base_url + 'audio/success.mp3')
            audio.play()
        },
        successMessage(data) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            Toast.fire({
                icon: data.status,
                title: data.message
            })
        },
        validationError(message = 'please fill form correctly') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message,
            })
        },
        formatPrice(value) {
            return parseFloat(value).toFixed(2)
        },
        strippedContent(string) {
            return string.replace(/<\/?[^>]+>/ig, " ")
        },
        dateToString(datePassed) {
            const date = new Date(datePassed)
            const formattedDate = date.toDateString().slice(3)
            const [month, day, year] = formattedDate.split(' ')
            return `${month} ${day}, ${year}`
        }
    }
};
app.mixin(Mixin);

const cartStore = useCartStore();

document.addEventListener('DOMContentLoaded', async () => {
    await Promise.all([
        cartStore.getCart(),
        cartStore.getTrial(),
    ]);
});

import HomeOffer from './components/front/offer/HomeOffer.vue';
import Home from './store/Home.vue';
import HomeSlider from './store/HomeSlider.vue';

import HotDeal from './components/front/product/HotDeal.vue';
import ProductByCategory from './components/front/product/OnSaleProduct.vue';
import Cart from './components/front/cart/Cart.vue';
import CheckoutCart from './components/front/cart/CheckoutCart.vue';
import SearchBox from './components/front/search/SearchBox.vue';
import SearchProduct from './components/front/product/SearchProduct.vue';
import UserSubscribe from './components/front/subscribe/Subscribe.vue';
import AllOffers from './components/front/offer/AllOffer.vue';
import OffersProduct from './components/front/offer/OfferProduct.vue';
import LevelThreeCategory from './components/front/category/LevelThreeCategory.vue';
import SubCategoryProduct from './components/front/product/SubCategoryProduct.vue';
import SubSubCategoryProduct from './components/front/product/SubSubCategoryProduct.vue';
import ProductDetails from './components/front/product/ProductDetails.vue';
import AddToCartModal from './components/front/product/AddToCartModal.vue';
import Verification from './components/front/setting/Verification.vue';
import OrderHistory from './components/front/user/Orders.vue';
import OrderTracking from './components/front/user/OrderTrack.vue';
import ProfileUpdate from './components/front/profile/ProfileUpdate.vue';
import UserDashboard from './components/front/profile/UserDashboard.vue';
import FlutterWavePayment from './components/front/payment/FlutterWavePayment.vue';

app.component('home-offers', HomeOffer);
app.component('home-page', Home);
app.component('home-slider', HomeSlider);
app.component('hot-deal', HotDeal);
app.component('product-by-category', ProductByCategory);
app.component('cart', Cart);
app.component('checkout-cart', CheckoutCart);
app.component('search-box', SearchBox);
app.component('search-product', SearchProduct);
app.component('user-subscribe', UserSubscribe);
app.component('all-offers', AllOffers);
app.component('offers-product', OffersProduct);
app.component('level-three-category', LevelThreeCategory);
app.component('sub-category-product', SubCategoryProduct);
app.component('sub-sub-category-product', SubSubCategoryProduct);
app.component('product-details', ProductDetails);
app.component('add-to-cart-modal', AddToCartModal);
app.component('verification', Verification);
app.component('order-history', OrderHistory);
app.component('order-tracking', OrderTracking);
app.component('profile-update', ProfileUpdate);
app.component('user-dashboard', UserDashboard);
app.component('flutter-wave-payment', FlutterWavePayment);

app.mount('#front-wrapper');
