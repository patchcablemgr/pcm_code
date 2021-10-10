<template>
  <div>
    <b-container class="bv-example-row" fluid="xs">
      <b-row>
        <b-col>
          <b-card
            title="Card 1"
          >
            <b-card-body>
              Card body
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>
          <b-card
            title="Card 2"
          >
            <b-card-body>
              Card body
            </b-card-body>
          </b-card>

        </b-col>
        <b-col>
          <b-card
            title="Card 3"
          >
            <b-card-body>
              Card body
            </b-card-body>
          </b-card>

        </b-col>
      </b-row>
    </b-container>

    <!-- Toast -->
    <toast-general/>

  </div>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BCardBody, BCardText, BFormRadio, } from 'bootstrap-vue'
import ToastGeneral from './templates/ToastGeneral.vue'

export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardText,
    BFormRadio,

    ToastGeneral,
  },
  data() {
    return {
    }
  },
  computed: {
  },
  methods: {
  },
  mounted() {
  },
}
</script>

<style>

</style>
