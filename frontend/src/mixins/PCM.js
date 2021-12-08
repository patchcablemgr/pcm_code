export const PCM = {
    methods: {

// Object
        GetObjectIndex: function(ObjectID, Context=false) {

            // Initial variables
            const vm = this
            Context = (Context) ? Context : vm.Context
            const Objects = vm.Objects
      
            // Get object index
            const ObjectIndex = Objects[Context].findIndex((object) => object.id == ObjectID);
      
            return ObjectIndex
        },
        GetObjectSize: function(ObjectID) {

            // Store variables
            const vm = this
            const Context = vm.Context
            const Templates = vm.Templates
            const Objects = vm.Objects
        
            // Get object index
            const ObjectIndex = Objects[Context].findIndex((object) => object.id == ObjectID);
        
            // Get template index
            const TemplateID = Objects[Context][ObjectIndex].template_id
            const TemplateIndex = Templates[Context].findIndex((template) => template.id == TemplateID);
        
            // Get template
            const ObjectPreviewData = Templates[Context][TemplateIndex]
        
            const ObjectSize = ObjectPreviewData.ru_size
        
            return ObjectSize
        },

// Template
        GetTemplateID: function(ObjectID, Context=false) {

            const vm = this
            Context = (Context) ? Context : vm.Context
            const Objects = vm.Objects
            let TemplateID = 0
        
            const ObjectIndex = Objects[Context].findIndex((object) => object.id == ObjectID )
        
            if(ObjectIndex !== -1) {
                const Object = Objects[Context][ObjectIndex]
                TemplateID = Object.template_id
            }
        
            return TemplateID
    
        },
        GetTemplateIndex: function(TemplateID, Context=false) {

            // Initial variables
            const vm = this
            const Templates = vm.Templates
            Context = (Context) ? Context : vm.Context
      
            // Get object index
            const TemplateIndex = Templates[Context].findIndex((template) => template.id == TemplateID);
      
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
            const HonorHover = (vm.Templates[Context][TemplateIndex].id.toString().includes('pseudo')) ? false : true

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
            const ObjectIDHovered = vm.PartitionAddressHovered[Context].object_id
            const PartitionAddressHovered = vm.PartitionAddressHovered[Context][TemplateFaceSelected]

            const PartitionAddress = vm.GetPartitionAddress(PartitionIndex)
            const ObjectID = vm.ObjectID
            
            let PartitionIsHovered = false

            if(PartitionAddressHovered.length === PartitionAddress.length && PartitionAddressHovered.every(function(value, index) { return value === PartitionAddress[index]}) && ObjectID == ObjectIDHovered) {
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
            const HonorClick = (vm.Templates[Context][TemplateIndex].id.toString().includes('pseudo')) ? false : true
      
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
        GetNodeIcon: function(NodeType) {

            let Icon = "HomeIcon"
            if(NodeType == 'location') {
                Icon = "HomeIcon"
            } else if(NodeType == 'pod') {
                Icon = "CircleIcon"
            } else if(NodeType == 'cabinet') {
                Icon = "ServerIcon"
            } else if(NodeType == 'floorplan') {
                Icon = "MapIcon"
            }
        
            return Icon
        },
        GeneratePseudoData(Context, Template) {

            const vm = this

            let WorkingObjectData = []
            let WorkingTemplateData = []
            let PseudoObjectID = null
            let PseudoTemplateID = null
            let PseudoObjectParentID = null
            let PseudoObjectParentFace = null
            let PseudoObjectParentPartitionAddress = null
            let PseudoObjectParentEnclosureAddress = null
            const GenericObject = vm.$store.state.pcmProps.GenericObject
            const GenericTemplate = vm.$store.state.pcmProps.GenericTemplate
            const WorkspaceStandardID = vm.$store.state.pcmProps.WorkspaceStandardID
            const WorkspaceInsertID = vm.$store.state.pcmProps.WorkspaceInsertID
            const TemplateID = Template.id
            const TemplateType = Template.type
        
            if (TemplateType == 'insert') {
                
                PseudoObjectParentFace = 'front'
                PseudoObjectParentPartitionAddress = [0, 0]
                PseudoObjectParentEnclosureAddress = [0, 0]
                const InsertConstraints = Template.insert_constraints
        
                // Generate pseudo IDs
                PseudoTemplateID = "pseudo-" + TemplateID
                PseudoObjectID = "pseudo-" + TemplateID
        
                // Create pseudo object
                WorkingObjectData.push(JSON.parse(JSON.stringify(GenericObject), function (Key, Value) {
                    if (Key == 'id') {
                        return PseudoObjectID
                    } else if (Key == 'cabinet_front') {
                        return 'front'
                    } else if (Key == 'location_id') {
                        return (Context == 'workspace') ? WorkspaceInsertID : null
                    } else if (Key == 'cabinet_ru') {
                        return 1
                    } else if (Key == 'template_id') {
                        return PseudoTemplateID
                    } else if (Key == 'parent_id') {
                        return PseudoObjectParentID
                    } else {
                        return Value
                    }
                }))
        
                // Create pseudo template
                WorkingTemplateData.push(JSON.parse(JSON.stringify(GenericTemplate), function (Key, Value) {
                    if (Key == 'id') {
                        return PseudoTemplateID
                    } else if (Key == 'name') {
                        return Template.name
                    } else if (Key == 'category_id') {
                        return Template.category_id
                    } else if (Key == 'type') {
                        // Set pseudo template type, but avoid setting partition type
                        if (Value === null) {
                            return 'standard'
                        } else {
                            return Value
                        }
                    } else if (Key == 'ru_size') {
                        // Set pseudo template RU size if this is the insert constraint origin ('standard' template type)
                        return Math.ceil(InsertConstraints.part_layout.height / 2)
                    } else if (Key == 'blueprint') {

                        // Generate enclosure partition
                        let EnclosurePartition = {
                            'type': 'enclosure',
                            'units': InsertConstraints.part_layout.height,
                            'enc_layout': {
                                'cols': InsertConstraints.enc_layout.cols,
                                'rows': InsertConstraints.enc_layout.rows
                            },
                            'children': []
                        }

                        // Set pseudo template partition attributes
                        Value.front[0].units = InsertConstraints.part_layout.width
                        Value.front[0].children.push(EnclosurePartition)
                        return Value
            
                    } else {
            
                        return Value
                    }
                }))
            }
        
            // Create object
            const ObjectID = TemplateID
            WorkingObjectData.push(JSON.parse(JSON.stringify(GenericObject), function (Key, Value) {
                if (Key == 'id') {
                    return TemplateID
                } else if (Key == 'template_id') {
                    return TemplateID
                } else if (Key == 'location_id') {
                    return (Context == 'workspace' && TemplateType == 'standard') ? WorkspaceInsertID : Value
                } else if (Key == 'cabinet_front') {
                    return (Context == 'workspace' && TemplateType == 'standard') ? 'front' : Value
                } else if (Key == 'cabinet_ru') {
                    return (Context == 'workspace' && TemplateType == 'standard') ? 1 : Value
                } else if (Key == 'parent_id') {
                    return PseudoObjectID
                } else if (Key == 'parent_face') {
                    return PseudoObjectParentFace
                } else if (Key == 'parent_part_addr') {
                    return PseudoObjectParentPartitionAddress
                } else if (Key == 'parent_enclosure_address') {
                    return PseudoObjectParentEnclosureAddress
                } else {
                    return Value
                }
            }))
        
            // Add Templates
            WorkingTemplateData.forEach(function(element) {
                vm.$store.commit('pcmTemplates/ADD_Template', {pcmContext:Context, data:element})
            })
            
            // Add Objects
            WorkingObjectData.forEach(function(element) {
                vm.$store.commit('pcmObjects/ADD_Object', {pcmContext:Context, data:element}, {root:true})
            })

            return ObjectID
            
        },
    }
}