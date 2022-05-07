<template>
  <div>
    <b-card title="Kick start your project 🚀">
	  <b-card-text>test</b-card-text>
	  <b-card-text><span @click="getUser">Logout</span></b-card-text>
      <b-card-text>All the best for your new project.</b-card-text>
      <b-card-text>Please make sure to read our <b-link
        href="https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/"
        target="_blank"
      >
        Template Documentation test-3
      </b-link> to understand where to go from here and how to use our template.</b-card-text>
    </b-card>

    <b-card title="Want to integrate JWT? 🔒">
      <b-card-text>We carefully crafted JWT flow so you can implement JWT with ease and with minimum efforts.</b-card-text>
      <b-card-text>Please read our  JWT Documentation to get more out of JWT authentication.</b-card-text>
    </b-card>
  </div>
</template>

<script>
import { BCard, BCardText, BLink } from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BCardText,
    BLink,
  },
  methods: {
    getUser() {
      //this.$http.get('/api/auth/logout').then(res => console.log(res))
	    this.$http.get('/api/auth/user').then(res => console.log(res))
    },
  },
}
</script>

<style>

</style>
