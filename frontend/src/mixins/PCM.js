export const PCM = {
    methods: {

// Object
        GetObjectIndex: function(ObjectID, Context=false) {

            // Initial variables
            const vm = this
            Context = (Context) ? Context : vm.Context
      
            // Get object index
            const ObjectIndex = vm.Objects[Context].findIndex((object) => object.id == ObjectID);
      
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
        GetSelectedObjectIndex: function(Context){

            const vm = this
            const ObjectID = vm.PartitionAddressSelected[Context].object_id
            const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)

            return ObjectIndex
        },
        GetObjectSelected: function(Context) {

            // Store variables
            const vm = this
            const ObjectIndex = vm.GetSelectedObjectIndex(Context)
            const Object = (ObjectIndex !== -1) ? vm.Objects[Context][ObjectIndex] : false
    
            return Object
        },

// Template
        GetTemplateID: function(ObjectID, Context=false) {

            const vm = this
            Context = (Context) ? Context : vm.Context
        
            const ObjectIndex = vm.Objects[Context].findIndex((object) => object.id == ObjectID )
            const TemplateID = (ObjectIndex !== -1) ? vm.Objects[Context][ObjectIndex].template_id : false
        
            return TemplateID
    
        },
        GetTemplateIndex: function(TemplateID, Context=false) {

            // Initial variables
            const vm = this
            Context = (Context) ? Context : vm.Context
      
            // Get object index
            const TemplateIndex = vm.Templates[Context].findIndex((template) => template.id == TemplateID )
      
            return TemplateIndex
        },
        GetSelectedTemplateIndex: function(Context){

            const vm = this
            const ObjectID = vm.PartitionAddressSelected[Context].object_id
            
            const TemplateID = vm.GetTemplateID(ObjectID, Context)
            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

            return TemplateIndex
        },
        GetTemplateSelected: function(Context) {

            // Store variables
            const vm = this
            const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
            const Template = (TemplateIndex !== -1) ? vm.Templates[Context][TemplateIndex] : false
    
            return Template
        },

// Location
        GetLocationIndex: function(LocationID, Context) {

            // Initial variables
            const vm = this

            // Get object index
            const LocationIndex = vm.Locations[Context].findIndex((location) => location.id == LocationID )

            return LocationIndex
        },
        GetLocationNode(NodeID) {

            const vm = this
            const TreeRef = vm.TreeRef
        
            const Criteria = {
                "id": NodeID.toString()
            }
            let Node = vm.$refs[TreeRef].find(Criteria)[0]
        
            return Node
        },
        BuildLocationTree: function(Parent){

            const vm = this
            const TreeRef = vm.TreeRef
            const Context = vm.Context
            Parent = (Parent !== undefined) ? Parent : { id: 0 }
            const ParentID = Parent.id
            const ChildrenFiltered = vm.Locations[Context].filter(location => location.parent_id == ParentID)
            const ChildrenData = []
        
            ChildrenFiltered.forEach(function(child) {
                const ChildData = {
                "id": child.id,
                "text": child.name,
                "data": {
                    "type": child.type,
                    "icon": vm.GetNodeIcon(child.type),
                    "parent_id": child.parent_id,
                    "img": child.img,
                },
                }
                ChildrenData.push(ChildData)
            })
        
            if(ChildrenData.length) {
                
                ChildrenData.forEach(function(child) {
        
                if(ParentID == 0) {
        
                    vm.$refs[TreeRef].append(child)
                } else {
        
                    let ParentNode = vm.GetLocationNode(ParentID)
                    ParentNode.append(child)
                }
                })
        
                ChildrenData.forEach(child => vm.BuildLocationTree(child))                
            }
            
            return
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
            let HonorHover
            HonorHover = (vm.Templates[Context][TemplateIndex].id.toString().includes('pseudo')) ? false : true
            HonorHover = (vm.$route.name == 'environment' && Context == 'template') ? false : HonorHover

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
            const Face = vm.TemplateFaceSelected[Context]
            const PartitionAddress = EmitData.PartitionAddress
            const TemplateID = EmitData.TemplateID
            const ObjectID = EmitData.ObjectID
            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

            // Get partition type
            const Template = vm.Templates[Context][TemplateIndex]
            const Blueprint = Template.blueprint[Face]
            const Partition = vm.GetPartition(Blueprint, PartitionAddress)
            const PartitionType = Partition.type
                  
            // Clicked partition should not be highlighted if it is a preview insert parent
            let HonorClick
            HonorClick = (vm.Templates[Context][TemplateIndex].id.toString().includes('pseudo')) ? false : true
            HonorClick = (Context == 'workspace' && PartitionAddress.length == 0) ? false : HonorClick
            HonorClick = (vm.$route.name == 'environment' && Context == 'template') ? false : HonorClick
      
            if(HonorClick) {
                vm.PartitionAddressSelected[Context][Face] = PartitionAddress
                vm.PartitionAddressSelected[Context].template_id = TemplateID
                vm.PartitionAddressSelected[Context].object_id = ObjectID
            }

            if(HonorClick && Context == 'template' && PartitionType == 'enclosure') {

                // Get selected template data
                const TemplateFunction = Template.function
                const TemplateCategoryID = Template.category_id
                const TemplateInsertConstraints = Template.insert_constraints
        
                // Remove preview pseudo objects/templates
                vm.RemovePreviewPseudoData()
        
                // Copy selected template to insert parent template and correct id
                const InsertParentTemplateID = 'pseudo-'+vm.$uuid.v4()
                const InsertParentTemplate = JSON.parse(JSON.stringify(Template), function(key, value) {
                  if(key == 'id') {
                    return InsertParentTemplateID
                  } else {
                    return value
                  }
                })
                vm.$store.commit('pcmTemplates/ADD_Template', {pcmContext:'workspace', data:InsertParentTemplate})
        
                // Generate pseudo object and constraining templates/objects if necessary
                const PseudoObjectID = vm.GeneratePseudoData('workspace', InsertParentTemplate)
        
                // Get insert constraints
                const InsertConstraints = vm.GetInsertConstraints(Template, Face, PartitionAddress)
        
                // Adjust insert template properties
                const WorkspaceInsertID = vm.$store.state.pcmProps.WorkspaceInsertID
                const InsertTemplateIndex = vm.GetTemplateIndex(WorkspaceInsertID, 'workspace')
                const InsertTemplate = JSON.parse(JSON.stringify(vm.Templates['workspace'][InsertTemplateIndex]))
                const GenericBlueprint = vm.$store.state.pcmProps.GenericBlueprintGeneric
                InsertTemplate.blueprint.front = [
                    JSON.parse(JSON.stringify(GenericBlueprint), function(Key, Value) {
                        if(Key == 'units') {
                            return InsertConstraints[0].part_layout.width
                        } else {
                            return Value
                        }
                    })
                ]
                InsertTemplate.category_id = TemplateCategoryID
                InsertTemplate.function = TemplateFunction
                InsertTemplate.insert_constraints = (TemplateInsertConstraints !== null) ? TemplateInsertConstraints.concat(InsertConstraints) : InsertConstraints
                vm.$store.commit('pcmTemplates/UPDATE_Template', {pcmContext:'workspace', data:InsertTemplate})
        
                // Adjust insert object properties
                const InsertObjectIndex = vm.GetObjectIndex(WorkspaceInsertID, 'workspace')
                const InsertObject = JSON.parse(JSON.stringify(vm.Objects['workspace'][InsertObjectIndex]))
                InsertObject.parent_id = PseudoObjectID
                InsertObject.parent_face = Face
                InsertObject.parent_partition_address = PartitionAddress
                InsertObject.parent_enclosure_address = [0,0]
                vm.$store.commit('pcmObjects/UPDATE_Object', {pcmContext:'workspace', data:InsertObject})
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
        GetPartitionSelected: function(Context=false) {

            // Store variables
            const vm = this
            Context = (Context) ? Context : vm.Context
            const Face = vm.TemplateFaceSelected[Context]
            const PartitionAddress = vm.PartitionAddressSelected[Context][Face]
            let Partition = false

            const TemplateIndex = vm.GetSelectedTemplateIndex(Context)
            if(TemplateIndex !== -1) {
                const Template = vm.Templates[Context][TemplateIndex]
                const Blueprint = Template.blueprint[Face]
                Partition = vm.GetPartition(Blueprint, PartitionAddress)
            }
    
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
        GeneratePortPreview: function(Context){

            const vm = this
            const Partition = vm.GetPartitionSelected(Context)

            const PortPreviewLimit = 5
            let PortID = ''
            let PreviewPortIDArray = []

            if(Partition.type == 'connectable') {
                const PortFormat = Partition.port_format
                let PortTotal = Partition.port_layout.cols * Partition.port_layout.rows
                let Truncated = false
                let LoopLimit = PortTotal

                // Limit port preview to 5
                if(PortTotal > PortPreviewLimit) {
                    Truncated = true
                    LoopLimit = PortPreviewLimit-1
                }

                // Generate port IDs
                for(let i=0; i<LoopLimit; i++){
                    PortID = vm.GeneratePortID(i, PortTotal, PortFormat)
                    PreviewPortIDArray.push(PortID)
                }

                // Append ellipses if port preview is truncated
                if(Truncated) {
                    PreviewPortIDArray.push('...')
                    PortID = vm.GeneratePortID(PortTotal-1, PortTotal, PortFormat)
                    PreviewPortIDArray.push(PortID)
                }
            }

            return PreviewPortIDArray.join(', ')
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

                InsertConstraints.forEach(function(InsertConstraint, Index){

                    // Generate pseudo IDs

                    PseudoTemplateID = "pseudo-" + vm.$uuid.v4()
                    PseudoObjectID = PseudoTemplateID
            
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
                        } else if (Key == 'parent_face') {
                            return PseudoObjectParentFace
                        } else if (Key == 'parent_partition_address') {
                            return PseudoObjectParentPartitionAddress
                        } else if (Key == 'parent_enclosure_address') {
                            return PseudoObjectParentEnclosureAddress
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
                                return (Index == 0) ? 'standard' : 'insert'
                            } else {
                                return Value
                            }
                        } else if (Key == 'ru_size') {
                            // Set pseudo template RU size if this is the insert constraint origin ('standard' template type)
                            return Math.ceil(InsertConstraint.part_layout.height / 2)
                        } else if (Key == 'insert_constraints') {
                            return (Index == 0) ? null : [InsertConstraints[Index-1]]
                        } else if (Key == 'blueprint') {

                            // Generate enclosure partition
                            let EnclosurePartition = {
                                'type': 'enclosure',
                                'units': InsertConstraint.part_layout.height,
                                'enc_layout': {
                                    'cols': InsertConstraint.enc_layout.cols,
                                    'rows': InsertConstraint.enc_layout.rows
                                },
                                'children': []
                            }

                            // Set pseudo template partition attributes
                            Value.front[0].units = InsertConstraint.part_layout.width
                            Value.front[0].children.push(EnclosurePartition)
                            return Value
                
                        } else {
                
                            return Value
                        }
                    }))

                    PseudoObjectParentID = PseudoObjectID
                })
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
        RemovePreviewPseudoData: function() {

            const vm = this
        
            const PseudoObjects = vm.Objects.workspace.filter(function(Object){
                return Object.id.toString().includes('pseudo')
            })
            PseudoObjects.forEach(function(object){
                vm.$store.commit('pcmObjects/REMOVE_Object', {pcmContext:'workspace', data:object})
            })
        
            const PseudoTemplates = vm.Templates.workspace.filter(function(Template){
                return Template.id.toString().includes('pseudo')
            })
            PseudoTemplates.forEach(function(template){
                vm.$store.commit('pcmTemplates/REMOVE_Template', {pcmContext:'workspace', data:template})
            })
    
        },
        GetInsertConstraints: function(Template, TemplateFace, PartitionAddress) {

            const vm = this
            const Blueprint = Template.blueprint[TemplateFace]
            const Partition = vm.GetPartition(Blueprint, PartitionAddress)
            const PartitionDirection = vm.GetPartitionDirection(PartitionAddress)
            let Width
            let Height
            let InsertConstraints = {
              "part_layout": {
                "height": null,
                "width": null
              },
              "enc_layout": {
                "cols": Partition.enc_layout.cols,
                "rows": Partition.enc_layout.rows
              }
            }
      
            if (PartitionDirection == 'col') {
      
              // Store height
              Height = Partition.units
      
              // Store width
              if (PartitionAddress.length > 1) {
                
                // Partition is deeper than 1st level, take parent partition units
                const ParentPartitionAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
                let ParentPartition = vm.GetPartition(Blueprint, ParentPartitionAddress)
                Width = ParentPartition.units
      
              } else {
      
                // Partition is 1st level and has no parent partition
                if (Template.insert_constraints !== null) {
      
                  // Get width from constraints if they exist
                  Width = Template.insert_constraints[Template.insert_constraints.length-1].part_layout.width
                } else {
      
                  // Get width from template RU size
                  Width = 24
                }
              }
            } else {
      
              // Store width
              Width = Partition.units
      
              // Store height
              if (PartitionAddress.length > 1) {
                
                // Partition is deeper than 1st level, take parent partition units
                const ParentPartitionAddress = PartitionAddress.slice(0, PartitionAddress.length - 1)
                let ParentPartition = vm.GetPartition(Blueprint, ParentPartitionAddress)
                Height = ParentPartition.units
      
              } else {
      
                // Partition is 1st level and has no parent partition
                if (Template.insert_constraints !== null) {
      
                  // Get height from constraints if they exist
                  Height = Template.insert_constraints[Template.insert_constraints.length-1].part_layout.height
                } else {
      
                  // Get height from template RU size
                  Height = Template.ru_size * 2
                }
              }
            }
      
            InsertConstraints.part_layout.height = Height
            InsertConstraints.part_layout.width = Width
      
            return [InsertConstraints]
      
        },
        DisplayError: function(errData) {

            const errMsg = errData.response.data

            // Display error to user via toast
            this.$bvToast.toast(JSON.stringify(errMsg), {
                title: 'Error',
                variant: 'danger',
            })
        },
    }
}