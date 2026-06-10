import Swal from 'sweetalert2';
import moment from 'moment';

export function useCommonActions() {
    const successMessage = (data) => {
        Swal.fire({
            icon: data.status,
            title: data.status === 'success' ? 'Success' : 'Error',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
        });
    };

    const validationError = () => {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please check the form for errors!',
        });
    };

    const dateToString = (date) => {
        return moment(date).format('MMMM Do YYYY');
    };

    const formatPrice = (value) => {
        let val = (value / 1).toFixed(2).replace(',', '.');
        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    return {
        successMessage,
        validationError,
        dateToString,
        formatPrice
    };
}