export const PCM = {
    methods: {

// Object
        GetObjectIndex: function(ObjectID, Context=false) {

            // Initial variables
            const vm = this
            Context = (Context) ? Context : vm.Context
      
            // Get object index
            const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);
      
            return ObjectIndex
        },
        GetObjectSize: function(ObjectID) {

            // Store variables
            const vm = this
            const Context = vm.Context
            const TemplateData = vm.TemplateData
        
            // Get object index
            const ObjectIndex = vm.ObjectData[Context].findIndex((object) => object.id == ObjectID);
        
            // Get template index
            const TemplateID = vm.ObjectData[Context][ObjectIndex].template_id
            const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID);
        
            // Get template
            const ObjectPreviewData = TemplateData[Context][TemplateIndex]
        
            const ObjectSize = ObjectPreviewData.ru_size
        
            return ObjectSize
        },

// Template
        GetTemplateID: function(ObjectID, Context=false) {

            const vm = this
            Context = (Context) ? Context : vm.Context
            const ObjectData = vm.ObjectData[Context]
            let TemplateID = 0
        
            const ObjectIndex = ObjectData.findIndex((object) => object.id == ObjectID )
        
            if(ObjectIndex !== -1) {
                const Object = ObjectData[ObjectIndex]
                TemplateID = Object.template_id
            }
        
            return TemplateID
    
        },
        GetTemplateIndex: function(TemplateID, Context=false) {

            // Initial variables
            const vm = this
            const TemplateData = vm.TemplateData
            Context = (Context) ? Context : vm.Context
      
            // Get object index
            const TemplateIndex = TemplateData[Context].findIndex((template) => template.id == TemplateID);
      
            return TemplateIndex
        },

// Partition
        PartitionHovered: function(EmitData) {

            // Store variables
            const vm = this
            const Context = EmitData.Context
            const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
            const PartitionAddress = EmitData.PartitionAddress
            const HoverState = EmitData.HoverState
            const TemplateID = EmitData.TemplateID
            const ObjectID = EmitData.ObjectID
                
            // Hovered partition should not be highlighted if it is a preview insert parent
            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
            const HonorHover = (vm.TemplateData[Context][TemplateIndex].hasOwnProperty('pseudo')) ? false : true

            if(HonorHover) {
                vm.PartitionAddressHovered[Context][TemplateFaceSelected] = (HoverState) ? PartitionAddress : false
                vm.PartitionAddressHovered[Context].template_id = TemplateID
                vm.PartitionAddressHovered[Context].object_id = ObjectID
            }

        },
        PartitionIsHovered: function(PartitionIndex) {
            const vm = this
            const Context = vm.Context
            const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
            const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]
            const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
            const IDSelected = (Context == 'template') ? vm.PartitionAddressHovered[Context].template_id : vm.PartitionAddressHovered[Context].object_id
            const ID = (Context == 'template') ? vm.GetTemplateID(vm.ObjectID) : vm.ObjectID
            let PartitionIsHovered = false

            if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && IDSelected == ID) {
                PartitionIsHovered = true
            }
            return PartitionIsHovered
        },
        PartitionClicked: function(EmitData) {

            // Store variables
            const vm = this
            const Context = EmitData.Context
            const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
            const PartitionAddress = EmitData.PartitionAddress
            const TemplateID = EmitData.TemplateID
            const ObjectID = EmitData.ObjectID
            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
                  
            // Clicked partition should not be highlighted if it is a preview insert parent
            const HonorClick = (vm.TemplateData[Context][TemplateIndex].hasOwnProperty('pseudo')) ? false : true
      
            if(HonorClick) {
                vm.PartitionAddressSelected[Context][TemplateFaceSelected] = PartitionAddress
                vm.PartitionAddressSelected[Context].template_id = TemplateID
                vm.PartitionAddressSelected[Context].object_id = ObjectID
            }
        },
        PartitionIsSelected: function(PartitionIndex=false) {
            const vm = this
            const Context = vm.Context
            const TemplateFaceSelected = vm.TemplateFaceSelected[Context]
            const PartitionAddressSelected = vm.PartitionAddressSelected[Context][TemplateFaceSelected]
            const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
            const IDSelected = (Context == 'template') ? vm.PartitionAddressSelected[Context].template_id : vm.PartitionAddressSelected[Context].object_id
            const ID = (Context == 'template') ? vm.GetTemplateID(vm.ObjectID) : vm.ObjectID
            let PartitionIsSelected = false

            if(PartitionAddressSelected.length === PartitionAddress.length && PartitionAddressSelected.every(function(value, index) { return value === PartitionAddress[index]}) && IDSelected == ID) {
            PartitionIsSelected = true
            }
            return PartitionIsSelected
        },
        GetPartition: function(Blueprint, PartitionAddress) {
			
			// Locate template partition
			let Partition = Blueprint
			let PartitionCollection = Blueprint
			PartitionAddress.forEach(function(PartitionIndex) {
				if(typeof PartitionCollection[PartitionIndex] !== 'undefined') {
					Partition = PartitionCollection[PartitionIndex]
					PartitionCollection = PartitionCollection[PartitionIndex]['children']
				} else {
					return false
				}
			})
			
			return Partition
      
        },
        GetPartitionAddress: function(PartitionIndex=false) {

            // Store variables
            const vm = this
            if(PartitionIndex !== false) {
                return vm.InitialPartitionAddress.concat([PartitionIndex])
            } else {
                return JSON.parse(JSON.stringify(vm.InitialPartitionAddress))
            }
        },
        GetPartitionDirection: function(PartitionAddress) {

            const PartitionDirection = (PartitionAddress.length % 2) ? 'row' : 'col'
            
            return PartitionDirection
        },
        GetPartitionGrid: function(units) {

            let GridArray = []
            for(let i=0; i<units; i++) {
              GridArray.push('1fr')
            }
      
            return GridArray.join(' ')
        },
        GetPartitionAreas: function(rows, cols) {
    
            let AreasArray = []
            let AreaCounter = 0
            for(let r=0; r<rows; r++) {
        
                AreasArray.push([])
                for(let c=0; c<cols; c++) {
                
                AreasArray[r].push([])
                AreasArray[r][c] = 'area'+AreaCounter
                AreaCounter++
                }
            }
            
            const AreasString = "'" + AreasArray.map(arr => arr.join(' ')).join("' '") + "'";
        
            return AreasString
        },

// Floorplan
        FloorplanHovered: function(EmitData) {

            // Store variables
            const vm = this
            const ObjectID = EmitData.object_id
            const HoverState = EmitData.hover_state
                
            vm.PartitionAddressHovered.floorplan.object_id = (HoverState) ? ObjectID : false

        },
        FloorplanIsHovered: function(ObjectID) {

            // Store variables
            const vm = this
            return vm.PartitionAddressHovered.floorplan.object_id == ObjectID
        },
        FloorplanClicked: function(EmitData) {

            // Store variables
            const vm = this
            const ObjectID = EmitData.object_id
                
            vm.PartitionAddressSelected.floorplan.object_id = ObjectID

        },
        FloorplanIsSelected: function(ObjectID) {

            // Store variables
            const vm = this
            return vm.PartitionAddressSelected.floorplan.object_id == ObjectID
        },

// Misc
        GeneratePortID: function(Index, PortTotal, PortFormat){

            // Store variables
            const vm = this
            let PreviewPortID = ''
            let IncrementalCount = 0

            // Account for infinite count incrementables
            PortFormat.forEach(function(Field, FieldIndex){
            const FieldType = Field.type
            let FieldCount = Field.count

            // Increment IncrementalCount
            if(FieldType == 'incremental' || FieldType == 'series') {
                IncrementalCount++

                // Adjust field count
                if(FieldType == 'incremental' && FieldCount == 0) {
                PortFormat[FieldIndex].count = PortTotal
                } else if(FieldType == 'series') {
                let CurrentFieldValue = Field.value
                PortFormat[FieldIndex].count = CurrentFieldValue.split(',').length
                }
            }
            })

            PortFormat.forEach(function(Field){
            const FieldType = Field.type
            const FieldValue = Field.value
            const FieldOrder = Field.order
            const FieldCount = Field.count
            let HowMuchToIncrement
            let RollOver
            let Numerator = 1

            if(FieldType == 'static') {
                PreviewPortID = PreviewPortID + FieldValue
            } else {

                PortFormat.forEach(function(LoopField){
                const LoopFieldType = LoopField.type
                const LoopFieldOrder = LoopField.order
                const LoopFieldCount = LoopField.count

                if(LoopFieldType == 'incremental' || LoopFieldType == 'series') {
                    if(LoopFieldOrder < FieldOrder) {
                    Numerator *= LoopFieldCount
                    }
                }
                })

                HowMuchToIncrement = Math.floor(Index / Numerator)

                if(HowMuchToIncrement >= FieldCount) {
                RollOver = Math.floor(HowMuchToIncrement / FieldCount)
                HowMuchToIncrement = HowMuchToIncrement - (RollOver * FieldCount)
                
                }

                if(FieldType == 'incremental') {

                if(!isNaN(FieldValue)) {
                    PreviewPortID = PreviewPortID + (parseInt(FieldValue) + HowMuchToIncrement)
                } else {
                    PreviewPortID = PreviewPortID + '-'
                }

                } else if(FieldType == 'series') {

                const FieldValueArray = FieldValue.split(',')
                PreviewPortID = PreviewPortID + FieldValueArray[HowMuchToIncrement]

                }
            }
            })
            
            return PreviewPortID
        },
    }
}