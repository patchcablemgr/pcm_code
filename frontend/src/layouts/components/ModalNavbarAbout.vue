<template>
  <b-modal
    id="modal-navbar-about"
    title="About"
    ok-only
    ok-title="OK"
  >
    <b-card-text>
        <h5>3rd Party Plugins</h5>
        <b-link
          href="https://www.npmjs.com/package/vue-color"
          target="_blank"
        >vue-color</b-link>
    </b-card-text>
  </b-modal>
</template>

<script>
import {
  BCardText,
  BLink,
} from 'bootstrap-vue'

export default {
  components: {
    BCardText,
    BLink,
  }
}
</script>
