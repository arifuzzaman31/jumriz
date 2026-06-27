// composables/useMixin.js
import { onMounted } from 'vue'

export function useMixin() {

  const playCartSound = () => {
    const audio = new Audio(base_url + 'audio/success.mp3')
    audio.play()
  }

  const successMessage = (data) => {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {  // Changed from onOpen to didOpen (SweetAlert2 v11+)
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })

    Toast.fire({
      icon: data.status,
      title: data.message
    })
  }

  const validationError = (message = 'please fill form correctly') => {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: message,
    })
  }

  // Filters are now regular functions (Vue 3 removed filters)
  const formatPrice = (value) => {
    return parseFloat(value).toFixed(2)
  }

  const strippedContent = (string) => {
    return string.replace(/<\/?[^>]+>/ig, " ")
  }

  const dateToString = (datePassed) => {
    const date = new Date(datePassed)
    const formattedDate = date.toDateString().slice(3)
    const [month, day, year] = formattedDate.split(' ')
    return `${month} ${day}, ${year}`
  }

  return {
    playCartSound,
    successMessage,
    validationError,
    formatPrice,
    strippedContent,
    dateToString
  }
}