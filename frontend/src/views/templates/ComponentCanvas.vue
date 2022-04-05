<!-- Template/Object Details -->

<template>
  <canvas
    id="canvas"
    ref="ConnectionCanvas"
    :style="{'position':'absolute', 'pointer-events': 'none'}"
    :height="CanvasHeight"
    :width="CanvasWidth"
  ></canvas>
</template>

<script>
import { PCM } from '@/mixins/PCM.js'

const CanvasReady = false

export default {
  mixins: [PCM],
  components: {
  },
	directives: {
	},
  props: {
    CanvasHeight: {},
    CanvasWidth: {},
    ConnectionLineData: {},
    TrunkLineData: {},
  },
  data() {
    return {
      CanvasReady,
    }
  },
  computed: {
  },
  methods: {
    drawConnPath() {

      const vm = this

      const Inset = 4

      const CanvasTop = vm.$refs.ConnectionCanvas.getBoundingClientRect().top
      const CanvasLeft = vm.$refs.ConnectionCanvas.getBoundingClientRect().left
      
      const ScrollTop = document.documentElement.scrollTop
      const ScrollLeft = document.documentElement.scrollLeft

      const CanvasPosY = CanvasTop + ScrollTop
      const CanvasPosX = CanvasLeft + ScrollLeft
      
      vm.ConnectionLineData.forEach(function(LineData){

        // draw line
        if(LineData.line_coords.length > 1) {
          
          const StartX = LineData.line_coords[0][0] - CanvasPosX + Inset
          const StartY = LineData.line_coords[0][1] - CanvasPosY + Inset
          const EndX = LineData.line_coords[1][0] - CanvasPosX + Inset
          const EndY = LineData.line_coords[1][1] - CanvasPosY + Inset

          vm.vueCanvas.beginPath()
          vm.vueCanvas.moveTo(StartX, StartY)
          vm.vueCanvas.lineTo(EndX, EndY)
          vm.vueCanvas.stroke()
        }
      })
    },
    drawTrunkPath() {

      const vm = this

      const Inset = 4

      const CanvasTop = vm.$refs.ConnectionCanvas.getBoundingClientRect().top
      const CanvasLeft = vm.$refs.ConnectionCanvas.getBoundingClientRect().left
      
      const ScrollTop = document.documentElement.scrollTop
      const ScrollLeft = document.documentElement.scrollLeft

      const CanvasPosY = CanvasTop + ScrollTop
      const CanvasPosX = CanvasLeft + ScrollLeft
      
      vm.TrunkLineData.forEach(function(LineData){

        // draw line
        if(LineData.line_coords.length > 1) {
          
          const StartX = LineData.line_coords[0][0] - CanvasPosX + Inset
          const StartY = LineData.line_coords[0][1] - CanvasPosY + Inset
          const EndX = LineData.line_coords[1][0] - CanvasPosX + Inset
          const EndY = LineData.line_coords[1][1] - CanvasPosY + Inset

          vm.vueCanvas.beginPath()
          vm.vueCanvas.moveTo(StartX, StartY)
          vm.vueCanvas.lineTo(EndX, EndY)
          vm.vueCanvas.stroke()
        }
      })
    },
    clearPath() {

      const vm = this

      // clear canvas
      vm.vueCanvas.clearRect(0, 0, 400, 200)
    },
  },
  watch: {
    ConnectionLineData: {
     immediate: true,
     handler: function (newVal, oldVal) {
        if(this.CanvasReady){
          setTimeout(() => {
            this.clearPath()
            this.drawConnPath()
            this.drawTrunkPath()
          }, 0)
        }
      }
    },
  },
  mounted() {
    var canvas = document.getElementById("canvas");
    var ctx = canvas.getContext("2d");    
    this.vueCanvas = ctx;
    this.CanvasReady = true
    this.drawConnPath()
    this.drawTrunkPath()
  }
}
</script>