<!-- Template/Object Details -->

<template>
  <div>
    <table>
      <tr
        v-for="user in Users"
        :key="user.id"
      >
        <td>
          {{user.name}}
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
  },
	directives: {
	},
  props: {},
  data() {
    return {
    }
  },
  computed: {
    Users() {
      return this.$store.state.pcmUsers.Users
    },
  },
  methods: {
  }
}
</script>