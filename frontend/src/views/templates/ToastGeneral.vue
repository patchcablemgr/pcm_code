<template>
    <!-- toast -->
    <b-toast id="example-toast">
      <template #toast-title>
        <div class="d-flex flex-grow-1 align-items-center mr-1">
          <strong class="mr-auto"></strong>
        </div>
      </template>
      <span></span>
    </b-toast>
</template>

<script>
import { BToast, } from 'bootstrap-vue'

export default {
  components: {
    BToast,
  },
}
</script>