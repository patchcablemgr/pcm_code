<!-- Template/Object Details -->

<template>
  <canvas
    ref="ConnectionCanvas"
    :style="{position:'absolute', height:CanvasHeight+'px', width:CanvasWidth+'px'}"
  ></canvas>
</template>

<script>
import { PCM } from '@/mixins/PCM.js'

export default {
  mixins: [PCM],
  components: {
  },
	directives: {
	},
  props: {
    CanvasHeight: {},
    CanvasWidth: {},
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
    console.log('CanvasHeight: '+this.CanvasHeight)
  }
}
</script>