export const PCM = {
    computed: {
        Categories() {
            return this.$store.state.pcmCategories.Categories
        },
        Templates() {
            return this.$store.state.pcmTemplates.Templates
        },
        Objects() {
            return this.$store.state.pcmObjects.Objects
        },
        FloorplanTemplates() {
            return this.$store.state.pcmFloorplanTemplates.FloorplanTemplates
        },
        FloorplanCategories() {
            return this.$store.state.pcmFloorplanCategories.FloorplanCategories
        },
    },
    methods: {

// Object
        GetObject: function({ObjectID, Context}) {
                    
            // Initial variables
            const vm = this

            const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
            const Object = vm.Objects[Context][ObjectIndex]

            return Object
        },
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
            const ObjectID = vm.StateSelected[Context].object_id
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
        GetObjectFace: function(ObjectID, CabinetFace) {

            const vm = this
            const Context = vm.Context

            // Get Object
            //const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
            //const Object = vm.Objects[Context][ObjectIndex]
            const Object = vm.GetObject({ObjectID, Context})

            // Get Template
            const Template = vm.GetTemplate({ObjectID, Context})
            //const TemplateID = Object.template_id
            //const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
            //const Template = vm.Templates[Context][TemplateIndex]

            // Get Object Face
            let ObjectFace
            if(Template.type == 'standard') {
                if(CabinetFace == 'front') {
                ObjectFace = Object.cabinet_front
                } else {
                ObjectFace = (Object.cabinet_front == 'front') ? 'rear' : 'front'
                }
            } else {
                ObjectFace = 'front'
            }

            return ObjectFace
        },
        GetObjectCategoryColor(ObjectID) {

            const vm = this
            let CategoryColor
            if(ObjectID) {
                const Context = 'actual'
                const TemplateID = vm.GetTemplateID(ObjectID, Context)
                const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
                const Template = vm.Templates[Context][TemplateIndex]
                const CategoryID = Template.category_id
                const CategoryIndex = vm.Categories[Context].findIndex((category) => category.id == CategoryID)
                const Category = vm.Categories[Context][CategoryIndex]
                CategoryColor = Category.color
            } else {
                CategoryColor = "#FFFFFFFF"
            }

            return CategoryColor
        },

// Category
        GetCategory: function({ObjectID, TemplateID, CategoryID, Context}) {
            
            // Initial variables
            const vm = this

            if(ObjectID) {
                CategoryID = vm.GetCategoryID({ObjectID, Context})
            } else if(TemplateID) {
                CategoryID = vm.GetCategoryID({TemplateID, Context})
            }

            // Get category index
            const CategoryIndex = vm.GetCategoryIndex(CategoryID, Context)

            // Object or floorplan
            let CategoriesData
            if(CategoryID) {
                if(typeof CategoryID == 'number') {
                    CategoriesData = vm.Categories[Context]
                } else {
                    if(CategoryID.startsWith('pseudo')) {
                        CategoriesData = vm.Categories[Context]
                    } else {
                        CategoriesData = vm.FloorplanCategories
                    }
                }
            } else {
                CategoriesData = vm.Categories[Context]
            }

            return (CategoryIndex != -1) ? CategoriesData[CategoryIndex] : false
        },
        GetCategoryID: function({ObjectID, TemplateID, Context}) {

            // Initial variables
            const vm = this
            let Template
        
            if(ObjectID) {
                Template = vm.GetTemplate({ObjectID, Context})
            } else if(TemplateID) {
                Template = vm.GetTemplate({TemplateID, Context})
            }

            return Template.category_id
    
        },
        GetCategoryIndex: function(CategoryID, Context) {
            
            // Initial variables
            const vm = this

            // Object or floorplan
            let CategoriesData
            if(CategoryID) {
                if(typeof CategoryID == 'number') {
                    CategoriesData = vm.Categories[Context]
                } else {
                    if(CategoryID.startsWith('pseudo')) {
                        CategoriesData = vm.Categories[Context]
                    } else {
                        CategoriesData = vm.FloorplanCategories
                    }
                }
            } else {
                CategoriesData = vm.Categories[Context]
            }
      
            // Return category index
            return CategoriesData.findIndex((category) => category.id == CategoryID )
        },

// Template
        GetTemplate: function({ObjectID, TemplateID, Context}) {

            // Initial variables
            const vm = this

            if(ObjectID) {
                TemplateID = vm.GetTemplateID(ObjectID, Context)
            }

            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

            // Object or floorplan
            let TemplatesData
            if(TemplateID) {
                if(typeof TemplateID == 'number') {
                    TemplatesData = vm.Templates[Context]
                } else {
                    if(TemplateID.startsWith('pseudo')) {
                        TemplatesData = vm.Templates[Context]
                    } else {
                        TemplatesData = vm.FloorplanTemplates
                    }
                }
            } else {
                TemplatesData = vm.Templates[Context]
            }
            
            return (TemplateIndex != -1) ? TemplatesData[TemplateIndex] : false
        },
        GetTemplateID: function(ObjectID, Context=false) {

            // Initial variables
            const vm = this
            Context = (Context) ? Context : vm.Context
        
            const Object = vm.GetObject({ObjectID, Context})
            if(Object) {
                return (Object.template_id) ? Object.template_id : Object.floorplan_object_type
            } else {
                return false
            }
    
        },
        GetTemplateIndex: function(TemplateID, Context=false) {

            // Initial variables
            const vm = this
            Context = (Context) ? Context : vm.Context

            // Object or floorplan
            let TemplatesData
            if(TemplateID) {
                if(typeof TemplateID == 'number') {
                    TemplatesData = vm.Templates[Context]
                } else {
                    if(TemplateID.toString().startsWith('pseudo')) {
                        TemplatesData = vm.Templates[Context]
                    } else {
                        TemplatesData = vm.FloorplanTemplates
                    }
                }
            } else {
                TemplatesData = vm.Templates[Context]
            }

            return TemplatesData.findIndex((template) => template.id == TemplateID )
        },
        GetSelectedTemplateIndex: function(Context) {

            const vm = this
            const ObjectID = vm.StateSelected[Context].object_id
            
            const TemplateID = vm.GetTemplateID(ObjectID, Context)
            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)

            return TemplateIndex
        },
        GetTemplateSelected: function(Context) {

            // Store variables
            const vm = this
            const ObjectID = vm.StateSelected[Context].object_id

            return vm.GetTemplate({ObjectID, Context})
        },

// Location
        GetLocationIndex: function(LocationID, Context) {

            // Initial variables
            const vm = this

            // Get object index
            const LocationIndex = vm.Locations[Context].findIndex((location) => location.id == LocationID )

            return LocationIndex
        },
        GetLocationNodeID(LocationID) {

            const vm = this
            const TreeRef = vm.TreeRef
        
            const Criteria = {
                "data": {
                    "id": LocationID
                }
            }
            let Node = vm.$refs[TreeRef].find(Criteria)[0]
        
            return Node.id
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
        RebuildLocationTree: function({Scope}){

            const vm = this
            const Context = vm.Context
            const TreeRef = vm.TreeRef

            // Find expanded nodes
            const ExpandedNodes = vm.$refs[TreeRef].findAll({state: {expanded: true}})
            let ExpandedNodeIDs = []
            ExpandedNodes.forEach(function(ExpandedNode){
                ExpandedNodeIDs.push(ExpandedNode.id)
            })
            
            // Delete all nodes
            const ChildNodeCriteria = {
                text: /.*/
            }
            vm.$refs[TreeRef].remove(ChildNodeCriteria, true)
            
            // Rebuild tree
            vm.BuildLocationTree({Scope})

            // Expand expanded nodes
            ExpandedNodeIDs.forEach(function(ExpandedNodeID){
                let ExpandedNode = vm.GetLocationNode(ExpandedNodeID)
                ExpandedNode.expand()
            })

            // Select selected node
            if(vm.$store.state.pcmState.Selected[Context].node_id !== null) {
                const SelectedNode = vm.GetLocationNode(vm.$store.state.pcmState.Selected[Context].node_id)
                SelectedNode.select()
            }
        },
        BuildLocationTree: function({Parent,Scope}){

            const vm = this
            const TreeRef = vm.TreeRef
            const Context = vm.Context

            // Gather parent data
            Parent = (Parent !== undefined) ? Parent : { id: 0, data: {id: 0, type: 'root'}}
            const ParentTreeID = Parent.id
            const ParentID = Parent.data.id
            const ParentType = Parent.data.type

            let ChildrenFiltered = []

            if(ParentType == 'root' || ParentType == 'location' || ParentType == 'pod') {

                ChildrenFiltered = vm.Locations[Context].filter(location => location.parent_id == ParentID)
            } else if(ParentType == 'cabinet') {

                if(Scope == 'partition' || Scope == 'port') {
                    ChildrenFiltered = vm.Objects[Context].filter(object => object.location_id == ParentID && object.parent_id == null)
                }
            } else if(ParentType == 'object') {
                
                if(Scope == 'partition' || Scope == 'port') {

                    // Cannot trunk to self
                    const SelectedObjectID = vm.StateSelected[Context].object_id
                    if(ParentID != SelectedObjectID) {
                        
                        // Collect connectable partitions
                        const ParentObjectIndex = vm.GetObjectIndex(ParentID, Context)
                        const ParentObject = vm.Objects[Context][ParentObjectIndex]
                        const ParentTemplateID = ParentObject.template_id
                        const ParentTemplateIndex = vm.GetTemplateIndex(ParentTemplateID, Context)
                        const ParentTemplate = vm.Templates[Context][ParentTemplateIndex]
                        const FaceArray = ['front','rear']
                        FaceArray.forEach(function(Face){
                            ChildrenFiltered = ChildrenFiltered.concat(vm.GetConnectablePartitions(ParentID, Face, ParentTemplate.blueprint[Face], Scope))
                        })
                        
                        ChildrenFiltered = ChildrenFiltered.concat(vm.GetInsertConnectablePartitions(ParentID))
                    }
                }
            }
            
            // Prepare child data
            const ChildrenData = []
            ChildrenFiltered.forEach(function(child) {

                const ChildData = []
                if(ParentType == 'cabinet') {
                    ChildData.push({
                        id: child.id,
                        object_id: child.id,
                        face: null,
                        partition_address: null,
                        port_id: null,
                        name: child.name,
                        type: 'object',
                        parent_id: child.location_id,
                        img: '',
                        order: child.cabinet_ru,
                    })
                } else if(ParentType == 'object') {
                    const PortTotal = child.port_layout.rows * child.port_layout.cols

                    if(Scope == 'partition') {
                        const FirstPort = vm.GeneratePortID(0, PortTotal, child.port_format)
                        const LastPort = vm.GeneratePortID(PortTotal - 1, PortTotal, child.port_format)
                        ChildData.push({
                            id: child.id+'-'+child.face+'-'+child.partition_address.join('-'),
                            object_id: child.id,
                            face: child.face,
                            partition_address: child.partition_address,
                            port_id: null,
                            name: (PortTotal > 1) ? child.prefix + FirstPort + ' - ' + LastPort : child.prefix + FirstPort,
                            type: 'partition',
                            parent_id: child.location_id,
                            img: '',
                            order: child.partition_address.length + child.partition_address[child.partition_address.length - 1],
                        })
                    } else {
                        
                        for (let i = 0; i < PortTotal; i++) {
                            const Port = vm.GeneratePortID(i, PortTotal, child.port_format)
                            ChildData.push({
                                id: child.id+'-'+child.face+'-'+child.partition_address.join('-')+'-'+i,
                                object_id: child.id,
                                face: child.face,
                                partition_address: child.partition_address,
                                port_id: i,
                                name: child.prefix + Port,
                                type: 'port',
                                parent_id: child.location_id,
                                img: '',
                                //order: child.partition_address.length + child.partition_address[child.partition_address.length - 1],
                                order: i,
                            })
                        }
                    }
                } else {
                    ChildData.push({
                        id: child.id,
                        object_id: child.id,
                        face: null,
                        partition_address: null,
                        port_id: null,
                        name: child.name,
                        type: child.type,
                        parent_id: child.parent_id,
                        img: child.img,
                        order: child.order,
                    })
                }

                ChildData.forEach(function(child){

                    // Prevent irrelevant nodes from being selected
                    let Selectable = true
                    if(Scope == 'partition' || Scope == 'port') {
                        if(child.type != 'partition' && child.type != 'port') {
                            Selectable = false
                        }
                    }

                    const ChildData = {
                        "id": child.type+'-'+child.id,
                        "text": child.name,
                        "state": {
                            "selectable": Selectable
                        },
                        "data": {
                            "id": child.id,
                            "object_id": child.object_id,
                            "partition_address": child.partition_address,
                            "port_id": child.port_id,
                            "face": child.face,
                            "type": child.type,
                            "icon": vm.GetNodeIcon(child.type),
                            "parent_id": child.parent_id,
                            "img": child.img,
                            "order": child.order,
                        },
                    }
                    ChildrenData.push(ChildData)
                })
            })
        
            if(ChildrenData.length) {

                ChildrenData.sort((a,b) => (a.data.order > b.data.order) ? 1 : -1)
                
                ChildrenData.forEach(function(child) {
        
                    if(ParentTreeID == 0) {
                        vm.$refs[TreeRef].append(child)
                    } else {
            
                        let ParentNode = vm.GetLocationNode(ParentTreeID)
                        ParentNode.append(child)
                    }
                })
        
                ChildrenData.forEach(child => vm.BuildLocationTree({Parent:child,Scope}))
            }
            
            return
        },
        SelectTrunkNodes: function(ObjectID, Trunks){

            const vm = this
            const TreeRef = vm.TreeRef

            Trunks.forEach(function(trunk){

                const peerID = (trunk.a_id == ObjectID) ? trunk.b_id : trunk.a_id
                const peerFace = (trunk.a_id == ObjectID) ? trunk.b_face : trunk.a_face
                const peerPartition = (trunk.a_id == ObjectID) ? trunk.b_partition : trunk.a_partition
                const peerPortID = (trunk.a_id == ObjectID) ? trunk.b_port : trunk.a_port

                const Criteria = function(node){
                  let match = false

                  if(node.data.object_id == peerID) {
                    if(node.data.face == peerFace) {
                      let i = node.data.partition_address.length
                      let PartitionMatch = true
                      while (i--) {
                        if (node.data.partition_address[i] !== peerPartition[i]) {
                          PartitionMatch = false
                        }
                      }
                      if(PartitionMatch) {
                        if(node.data.port_id == peerPortID) {
                          match = true
                        }
                      }
                    }
                  }

                  return match
                }

                // Select and expand trunked nodes
                let Nodes = vm.$refs[TreeRef].findAll(Criteria)
                Nodes.forEach(function(Node){

                  // Select node
                  Node.select(true)

                  // Expand parent nodes
                  if(Node.parent) {
                    let NodeParentID = Node.parent.id
                    while(NodeParentID.toString() !== '0') {
                      let NodeParent = vm.GetLocationNode(NodeParentID)
                      NodeParent.expand()
                      if(NodeParent.parent) {
                        NodeParentID = NodeParent.parent.id
                      } else {
                        NodeParentID = 0
                      }
                    }
                  }
                })
            })
        },

// Partition
        PartitionHovered: function(EmitData) {

            // Store variables
            const vm = this
            const Context = EmitData.Context
            const Face = EmitData.ObjectFace
            const PartitionAddress = EmitData.PartitionAddress
            const HoverState = EmitData.HoverState
            const ObjectID = EmitData.ObjectID
            const PortID = EmitData.PortID
                
            // Hovered partition should not be highlighted if it is a preview insert parent
            const Template = vm.GetTemplate({ObjectID, Context})
            let HonorHover
            HonorHover = (Template.id.toString().startsWith('pseudo')) ? false : true
            HonorHover = (vm.$route.name == 'environment' && Context == 'template') ? false : HonorHover

            if(HonorHover) {
                let WorkingHovered = vm.StateHovered[Context]

                WorkingHovered.object_id = ObjectID
                WorkingHovered.object_face = Face
                WorkingHovered.partition[Face] = (HoverState) ? PartitionAddress : false
                WorkingHovered.port_id[Face] = PortID

                vm.$store.commit('pcmState/UPDATE_Hovered', {pcmContext:Context, data:WorkingHovered})
            }

        },
        PartitionIsHovered: function(data) {
            
            const vm = this
            const Context = data.Context
            const ObjectID = data.ObjectID
            const ObjectFace = data.ObjectFace
            const PartitionAddress = data.PartitionAddress
            const PortID = data.PortID

            const StoredObjectID = vm.StateHovered[Context].object_id
            const StoredObjectFace = vm.StateHovered[Context].object_face
            const StoredPartitionAddress = vm.StateHovered[Context].partition[StoredObjectFace]
            const StoredPortID = vm.StateHovered[Context].port_id[StoredObjectFace]

            if(ObjectID == StoredObjectID && ObjectFace == StoredObjectFace && JSON.stringify(PartitionAddress) == JSON.stringify(StoredPartitionAddress) && ((PortID !== null) ? (PortID == StoredPortID) : true)) {
                return true
            } else {
                return false
            }

        },
        PartitionClicked: function(EmitData) {

            // Store variables
            const vm = this
            const Context = EmitData.Context
            const Face = EmitData.ObjectFace
            const PartitionAddress = EmitData.PartitionAddress
            const PortID = EmitData.PortID
            const ObjectID = EmitData.ObjectID

            // Get partition type
            //const Template = vm.Templates[Context][TemplateIndex]
            const Template = vm.GetTemplate({ObjectID, Context})
            const Blueprint = Template.blueprint[Face]
            const Partition = vm.GetPartition(Blueprint, PartitionAddress)
            const PartitionType = Partition.type
                  
            // Clicked partition should not be highlighted if it is a preview insert parent
            let HonorClick
            //HonorClick = (vm.Templates[Context][TemplateIndex].id.toString().includes('pseudo')) ? false : true
            HonorClick = (Template.id.toString().includes('pseudo')) ? false : true
            HonorClick = (Context == 'workspace' && PartitionAddress.length == 0) ? false : HonorClick
            HonorClick = (vm.$route.name == 'environment' && Context == 'template') ? false : HonorClick

            // Determine port index
            let PortIndex = null
            if(PartitionType == 'connectable') {
                if(PortID !== null) {
                    PortIndex = PortID
                } else {
                    PortIndex = 0
                }
            }
      
            if(HonorClick) {

                let WorkingSelected = vm.StateSelected[Context]

                WorkingSelected.object_id = ObjectID
                WorkingSelected.object_face = Face
                WorkingSelected.partition[Face] = PartitionAddress
                WorkingSelected.port_id[Face] = PortIndex

                vm.$store.commit('pcmState/UPDATE_Selected', {pcmContext:Context, data:WorkingSelected})
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
        PartitionIsSelected: function(data) {

            const vm = this
            const Context = data.Context
            const ObjectID = data.ObjectID
            const ObjectFace = data.ObjectFace
            const PartitionAddress = data.PartitionAddress
            const PortID = data.PortID

            const StoredObjectID = vm.StateSelected[Context].object_id
            const StoredObjectFace = vm.StateSelected[Context].object_face
            const StoredPartitionAddress = vm.StateSelected[Context].partition[StoredObjectFace]
            const StoredPortID = vm.StateSelected[Context].port_id[StoredObjectFace]

            if(ObjectID == StoredObjectID && ObjectFace == StoredObjectFace && JSON.stringify(PartitionAddress) == JSON.stringify(StoredPartitionAddress) && ((PortID !== null) ? (PortID == StoredPortID) : true)) {
                return true
            } else {
                return false
            }

        },
        GetPartition: function(Blueprint, PartitionAddress) {
			
			// Locate template partition
			let Partition = Blueprint
			let PartitionCollection = Blueprint
            
            if(PartitionAddress.length) {
                PartitionAddress.forEach(function(PartitionIndex) {
                    if(typeof PartitionCollection[PartitionIndex] !== 'undefined') {
                        Partition = PartitionCollection[PartitionIndex]
                        PartitionCollection = PartitionCollection[PartitionIndex]['children']
                    } else {
                        return false
                    }
                })
            } else {
                Partition = {
                    type: 'generic',
                    children: Blueprint
                }
            }
			
			return Partition
      
        },
        GetPartitionSelected: function(Context=false) {

            // Store variables
            const vm = this
            Context = (Context) ? Context : vm.Context
            const ObjectID = vm.StateSelected[Context].object_id
            const Face = vm.StateSelected[Context].object_face
            const PartitionAddress = vm.StateSelected[Context].partition[Face]
            const Template = vm.GetTemplate({ObjectID, Context})
            let Partition = false

            if(Template) {
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
        GetConnectablePartitions: function(ObjectID, Face, Blueprint, Scope, ConnectablePartitions=[], BasePartAddr=[]) {

            const vm = this
            const Context = vm.Context
            
            // Gather selected compatibility data
            let SelectedTemplateFunction
            let SelectedMediaID
            let SelectedConnectorID
            let SelectedPortTotal
            const SelectedObject = vm.GetObjectSelected(Context)
            const FloorplanType = SelectedObject.floorplan_object_type
            
            if(FloorplanType === null) {
                const SelectedTemplate = vm.GetTemplateSelected(Context)
                const SelectedPartition = vm.GetPartitionSelected(Context)
                SelectedTemplateFunction = SelectedTemplate.function
                SelectedMediaID = SelectedPartition.media
                SelectedConnectorID = SelectedPartition.port_connector
                SelectedPortTotal = SelectedPartition.port_layout.cols * SelectedPartition.port_layout.rows
            } else if(FloorplanType == 'walljack') {
                SelectedTemplateFunction = 'passive'
                SelectedMediaID = 1
                SelectedConnectorID = 1
                SelectedPortTotal = 0
            } else if(FloorplanType == 'wap' || FloorplanType == 'camera') {
                SelectedTemplateFunction = 'endpoint'
                SelectedMediaID = 1
                SelectedConnectorID = 1
                SelectedPortTotal = 0
            }

            Blueprint.forEach(function(partition, index){
                const PartAddr = BasePartAddr.concat([index])
                if(partition.type == 'connectable') {

                    // Gather node compatibility data
                    const NodeIndex = vm.GetObjectIndex(ObjectID, Context)
                    const Node = vm.Objects[Context][NodeIndex]
                    const NodeTemplateID = vm.GetTemplateID(ObjectID, Context)
                    const NodeTemplateIndex = vm.GetTemplateIndex(NodeTemplateID, Context)
                    const NodeTemplate = vm.Templates[Context][NodeTemplateIndex]
                    const NodeTemplateFunction = NodeTemplate.function
                    const NodeMediaID = partition.media
                    const NodeConnectorID = partition.port_connector
                    const NodePortTotal = partition.port_layout.cols * partition.port_layout.rows
                    let Compatible = true

                    // Cannot trunk endpoint to endpoint
                    if(Scope == 'partition' && SelectedTemplateFunction == 'endpoint' && NodeTemplateFunction == 'endpoint') {
                        Compatible = false
                    }

                    // Must have same number of ports
                    if(Scope == 'partition' && SelectedPortTotal != NodePortTotal && SelectedPortTotal != 0) {
                        Compatible = false
                    }

                    // Media types must be compatible
                    if(SelectedTemplateFunction == 'endpoint' || NodeTemplateFunction == 'endpoint') {
                        // Selected or node template is endpoint
                        const SelectedConnector = vm.Connectors.find(connector => connector.value == SelectedConnectorID)
                        const SelectedMediaTypeID = SelectedConnector.type_id
                        const NodeConnector = vm.Connectors.find(connector => connector.value == NodeConnectorID)
                        const NodeMediaTypeID = NodeConnector.type_id
                        if((SelectedMediaTypeID != 4 && NodeMediaTypeID != 4) && (SelectedMediaTypeID != NodeMediaTypeID)) {
                            Compatible = false
                        }
                    } else {
                        // Node and Selected templates are passive
                        const SelectedMedia = vm.Media.find(medium => medium.value == SelectedMediaID)
                        const SelectedMediaCategoryID = SelectedMedia.category_id
                        const NodeMedia = vm.Media.find(medium => medium.value == NodeMediaID)
                        const NodeMediaCategoryID = NodeMedia.category_id
                        if(SelectedMediaCategoryID != NodeMediaCategoryID) {
                            Compatible = false
                        }
                    }

                    if(Compatible) {
                        const ConnectablePartition = JSON.parse(JSON.stringify(partition))
                        ConnectablePartition.id = ObjectID
                        ConnectablePartition.face = Face
                        ConnectablePartition.partition_address = PartAddr
                        ConnectablePartition.prefix = ''
                        ConnectablePartitions.push(ConnectablePartition)
                    }
                } else if(partition.type == 'generic') {
                    vm.GetConnectablePartitions(ObjectID, Face, partition.children, Scope, ConnectablePartitions, PartAddr)
                }
            })

            return ConnectablePartitions
        },
        GetInsertConnectablePartitions: function(ObjectID, NameArray=[]) {

            const vm = this
            const Context = vm.Context
            let ConnectablePartitions = []
            let PrefixSeparator
            let TempNameArray
            const Face = 'front'
            NameArray.push('')
            const SelectedObjectID = vm.StateSelected[Context].object_id

            // Retrieve child inserts
            const InsertObjects = vm.Objects[Context].filter(object => object.parent_id == ObjectID)
            InsertObjects.forEach(function(InsertObject){

                // Cannot trunk to self
                const InsertObjectID = InsertObject.id
                if(InsertObjectID != SelectedObjectID) {

                    // Gather insert object details
                    const InsertObjectName = InsertObject.name
                    const TemplateID = InsertObject.template_id
                    const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
                    const Template = vm.Templates[Context][TemplateIndex]
                    const TemplateFunction = Template.function

                    // Determine nested insert separator
                    PrefixSeparator = (TemplateFunction == 'endpoint') ? '' : '.'

                    // Retrieve connectable partitions
                    let InsertConnectablePartitions = vm.GetConnectablePartitions(InsertObjectID, Face, Template.blueprint[Face])

                    // Compile insert object names to be used for prefix
                    NameArray.splice(NameArray.length-1, 1, InsertObjectName)

                    // Compile prefix
                    InsertConnectablePartitions.forEach(function(Partition){

                        // Add an empty element to NameArray so passive prefix ends in '.' 
                        TempNameArray = NameArray.concat([''])
                        Partition.prefix = TempNameArray.join(PrefixSeparator)
                    })

                    ConnectablePartitions = ConnectablePartitions.concat(InsertConnectablePartitions)
                    ConnectablePartitions = ConnectablePartitions.concat(vm.GetInsertConnectablePartitions(InsertObjectID, JSON.parse(JSON.stringify(NameArray))))  
                }
            })

            return ConnectablePartitions
        },

// Trunks
        GetTrunks: function(ObjectID, ObjectFace, ObjectPartition){

            const vm = this

            const TrunkEntries = vm.Trunks.filter(function(trunk){

                let match = false

                if(trunk.a_id == ObjectID) {
                    if(trunk.a_face == ObjectFace) {
                        let i = trunk.a_partition.length
                        let PartitionMatch = true
                        while (i--) {
                            if (trunk.a_partition[i] !== ObjectPartition[i]) {
                                PartitionMatch = false
                            }
                        }
                        match = PartitionMatch
                    }
                }
                
                if(trunk.b_id == ObjectID) {
                    if(trunk.b_face == ObjectFace) {
                        let i = trunk.b_partition.length
                        let PartitionMatch = true
                        while (i--) {
                            if (trunk.b_partition[i] !== ObjectPartition[i]) {
                                PartitionMatch = false
                            }
                        }
                        match = PartitionMatch
                    }
                }
                return match
            })
            return TrunkEntries
        },

// Floorplan
        FloorplanHovered: function({ObjectID, HoverState, Context}) {

            // Store variables
            const vm = this
            vm.PartitionAddressHovered[Context].object_id = (HoverState) ? ObjectID : false

        },
        FloorplanIsHovered: function(ObjectID) {

            // Store variables
            const vm = this
            const Context = vm.Context
            return vm.PartitionAddressHovered[Context].object_id == ObjectID
        },
        FloorplanClicked: function({ObjectID, Context}) {

            // Store variables
            const vm = this

            let WorkingSelected = vm.StateSelected[Context]

            WorkingSelected.object_id = ObjectID
            WorkingSelected.object_face = 'front'
            WorkingSelected.partition.front = [0]
            WorkingSelected.partition.rear = [0]
            WorkingSelected.port_id.front = [0]
            WorkingSelected.port_id.rear = [0]

            vm.$store.commit('pcmState/UPDATE_Selected', {pcmContext:Context, data:WorkingSelected})

        },
        FloorplanIsSelected: function(ObjectID) {

            // Store variables
            const vm = this
            const Context = vm.Context
            return vm.StateSelected[Context].object_id == ObjectID
        },

// Port
        GetConnectionPath: function(ObjectID, Face, PartitionAddress, PortID, Context){
            
            const vm = this
            let RemoteSide
            let LocalObjectID = ObjectID
            let LocalFace = Face
            let LocalPartition = PartitionAddress
            let LocalPortID = PortID
            let PortPair = Math.floor(Math.random() * 1000) + 1
            let FwdTrunkPair, BwdTrunkPair = Math.floor(Math.random() * 1000) + 1
            FwdTrunkPair = BwdTrunkPair = Math.floor(Math.random() * 1000) + 1
            const ConnectionPath = []

            

            const TemplateID = vm.GetTemplateID(ObjectID, Context)
            const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
            const Template = vm.Templates[Context][TemplateIndex]
            
            if(typeof Template !== 'undefined') {

                const Blueprint = Template.blueprint[LocalFace]
                const Partition = vm.GetPartition(Blueprint, LocalPartition)

                if(Partition.type == 'connectable') {

                    // Look forward
                    while(LocalObjectID && LocalFace && LocalPartition) {

                        ConnectionPath.push(
                            {
                                'uuid': vm.$uuid.v4(),
                                'id': LocalObjectID,
                                'face': LocalFace,
                                'partition': LocalPartition,
                                'port_id': LocalPortID,
                                'port_pair': PortPair,
                                'trunk_pair': FwdTrunkPair,
                            }
                        )

                        const PortConnection = vm.GetConnection(LocalObjectID, LocalFace, LocalPartition, LocalPortID)
                        if(PortConnection) {

                            RemoteSide = PortConnection.remote_side

                            const RemoteObjectID = PortConnection.data[RemoteSide + '_id']
                            const RemoteFace = PortConnection.data[RemoteSide + '_face']
                            const RemotePartition = PortConnection.data[RemoteSide + '_partition']
                            const RemotePortID = PortConnection.data[RemoteSide + '_port']

                            // Generate unique ID
                            FwdTrunkPair = Math.floor(Math.random() * 1000) + 1

                            ConnectionPath.push(
                                {
                                    'uuid': vm.$uuid.v4(),
                                    'id': RemoteObjectID,
                                    'face': RemoteFace,
                                    'partition': RemotePartition,
                                    'port_id': RemotePortID,
                                    'port_pair': PortPair,
                                    'trunk_pair': FwdTrunkPair,
                                }
                            )

                            const RemoteTrunk = vm.GetTrunk(RemoteObjectID, RemoteFace, RemotePartition, RemotePortID)

                            if(RemoteTrunk) {

                                RemoteSide = RemoteTrunk.remote_side

                                LocalObjectID = RemoteTrunk.data[RemoteSide + '_id']
                                LocalFace = RemoteTrunk.data[RemoteSide + '_face']
                                LocalPartition = RemoteTrunk.data[RemoteSide + '_partition']
                                LocalPortID = (RemoteTrunk.data[RemoteSide + '_port'] == null) ? RemotePortID : RemoteTrunk.data[RemoteSide + '_port']

                            } else {
                                LocalObjectID = LocalFace = LocalPartition = LocalPortID = null
                            }
                        } else {
                            LocalObjectID = LocalFace = LocalPartition = LocalPortID = null
                        }

                        // Generate unique ID
                        PortPair = Math.floor(Math.random() * 1000) + 1
                    }

                    LocalObjectID = ObjectID
                    LocalFace = Face
                    LocalPartition = PartitionAddress
                    LocalPortID = PortID

                    // Look backward
                    while(LocalObjectID && LocalFace && LocalPartition) {

                        const Trunk = vm.GetTrunk(LocalObjectID, LocalFace, LocalPartition, LocalPortID)
                        if(Trunk) {

                            RemoteSide = Trunk.remote_side

                            const RemoteTrunkObjectID = Trunk.data[RemoteSide + '_id']
                            const RemoteTrunkFace = Trunk.data[RemoteSide + '_face']
                            const RemoteTrunkPartition = Trunk.data[RemoteSide + '_partition']
                            const RemoteTrunkPortID = (Trunk.data[RemoteSide + '_port'] == null) ? LocalPortID : Trunk.data[RemoteSide + '_port']

                            ConnectionPath.unshift(
                                {
                                    'uuid': vm.$uuid.v4(),
                                    'id': RemoteTrunkObjectID,
                                    'face': RemoteTrunkFace,
                                    'partition': RemoteTrunkPartition,
                                    'port_id': RemoteTrunkPortID,
                                    'port_pair': PortPair,
                                    'trunk_pair': BwdTrunkPair,
                                }
                            )

                            const Connection = vm.GetConnection(RemoteTrunkObjectID, RemoteTrunkFace, RemoteTrunkPartition, RemoteTrunkPortID)
                            if(Connection) {

                                RemoteSide = Connection.remote_side

                                const RemoteConnectionObjectID = Connection.data[RemoteSide + '_id']
                                const RemoteConnectionFace = Connection.data[RemoteSide + '_face']
                                const RemoteConnectionPartition = Connection.data[RemoteSide + '_partition']
                                const RemoteConnectionPortID = Connection.data[RemoteSide + '_port']

                                // Generate unique ID
                                BwdTrunkPair = Math.floor(Math.random() * 1000) + 1

                                ConnectionPath.unshift(
                                    {
                                        'uuid': vm.$uuid.v4(),
                                        'id': RemoteConnectionObjectID,
                                        'face': RemoteConnectionFace,
                                        'partition': RemoteConnectionPartition,
                                        'port_id': RemoteConnectionPortID,
                                        'port_pair': PortPair,
                                        'trunk_pair': BwdTrunkPair,
                                    }
                                )

                                LocalObjectID = RemoteConnectionObjectID
                                LocalFace = RemoteConnectionFace
                                LocalPartition = RemoteConnectionPartition
                                LocalPortID = RemoteConnectionPortID

                            } else {
                                LocalObjectID = LocalFace = LocalPartition = LocalPortID = null
                            }

                        } else {
                            LocalObjectID = LocalFace = LocalPartition = LocalPortID = null
                        }

                        // Generate unique ID
                        PortPair = Math.floor(Math.random() * 1000) + 1
                    }
                }
            }

            return ConnectionPath
        },
        GetConnection: function(LocalObjectID, LocalFace, LocalPartition, LocalPortID){
            
            const vm = this
            const ConnectionSideArray = ['a', 'b']
            let LocalConnectionSide
            let RemoteConnectionSide
            const DebugFlag = (LocalObjectID == 15 && LocalPortID == 0) ? true : false

            const Connection = vm.Connections.find(function(connection){

                let Found

                ConnectionSideArray.forEach(function(ConnectionSide){
                    const ColumnLocalID = ConnectionSide + '_id'
                    const ColumnLocalFace = ConnectionSide + '_face'
                    const ColumnLocalPartition = ConnectionSide + '_partition'
                    const ColumnLocalPort = ConnectionSide + '_port'

                    if(connection[ColumnLocalID] == LocalObjectID && connection[ColumnLocalFace] == LocalFace && JSON.stringify(connection[ColumnLocalPartition]) == JSON.stringify(LocalPartition) && connection[ColumnLocalPort] == LocalPortID) {
                        LocalConnectionSide = ConnectionSide
                        RemoteConnectionSide = (LocalConnectionSide == 'a') ? 'b' : 'a'
                        Found = true
                    }
                })
                return Found
            })

            if(typeof Connection !== 'undefined') {
                return {'data': Connection, 'local_side': LocalConnectionSide, 'remote_side': RemoteConnectionSide}
            } else {
                return false
            }
        },
        GetTrunk: function(LocalObjectID, LocalFace, LocalPartition, LocalPortID){
            
            const vm = this
            const TrunkSideArray = ['a', 'b']
            let LocalTrunkSide
            let RemoteTrunkSide

            const Trunk = vm.Trunks.find(function(trunk){

                let Found

                TrunkSideArray.forEach(function(TrunkSide){
                    const ColumnLocalID = TrunkSide + '_id'
                    const ColumnLocalFace = TrunkSide + '_face'
                    const ColumnLocalPartition = TrunkSide + '_partition'
                    const ColumnLocalPort = TrunkSide + '_port'

                    if(trunk[ColumnLocalID] == LocalObjectID && trunk[ColumnLocalFace] == LocalFace && JSON.stringify(trunk[ColumnLocalPartition]) == JSON.stringify(LocalPartition)) {
                        LocalTrunkSide = TrunkSide
                        RemoteTrunkSide = (LocalTrunkSide == 'a') ? 'b' : 'a'
                        Found =  true
                    }
                })

                return Found
            })

            if(typeof Trunk !== 'undefined') {
                return {'data': Trunk, 'local_side': LocalTrunkSide, 'remote_side': RemoteTrunkSide}
            } else {
                return false
            }
        },
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
        GetPortDisposition: function(Context, ObjectID, ObjectFace, ObjectPartition, PortID){

            const vm = this

            const Red = 'rgb(255,0,0)'
            const Black = 'rgb(0,0,0)'
            const Gray = 'rgb(128,128,128)'
            const Orange = 'rgb(255,215,0)'

            if(Context == 'actual') {
                const IsTrunked = (vm.GetTrunks(ObjectID, ObjectFace, ObjectPartition).length) ? true : false
                const Connection = vm.GetConnection(ObjectID, ObjectFace, ObjectPartition, PortID)
                if(Connection) {
                    if(Connection.data.a_id == null || Connection.data.b_id == null) {
                        return Orange
                    } else {
                        return Red
                    }
                } else if(IsTrunked) {
                    return Gray
                } else {
                    return Black
                }
            } else {
                return Black
            }
        },
        GeneratePortName: function(Context, ObjectID, ObjectFace, ObjectPartition, PortIndex){
            
            const vm = this
            const NameList = []

            //const TemplateID = vm.GetTemplateID(ObjectID, Context)
            //const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
            const Template = vm.GetTemplate({ObjectID, Context})
            if(Template) {
                //const Template = vm.Templates[Context][TemplateIndex]
                const TemplateType = Template.type
                const TemplateFunction = Template.function
                const Blueprint = Template.blueprint[ObjectFace]
                const Partition = vm.GetPartition(Blueprint, ObjectPartition)
                const PortFormat = Partition.port_format
                const PortTotal = Partition.port_layout.cols * Partition.port_layout.rows

                const PortNumber = vm.GeneratePortID(PortIndex, PortTotal, PortFormat)
                NameList.unshift(PortNumber)
                
                if(TemplateType == 'insert') {
                    const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
                    const Object = vm.Objects[Context][ObjectIndex]
                    NameList.unshift(Object.name)
                    let ParentID = Object.parent_id
                    while(ParentID){

                        // Get parent template type
                        const ParentTemplateID = vm.GetTemplateID(ParentID, Context)
                        const ParentTemplateIndex = vm.GetTemplateIndex(ParentTemplateID, Context)
                        const ParentTemplate = vm.Templates[Context][ParentTemplateIndex]
                        const ParentTemplateType = ParentTemplate.type

                        // Get parent
                        const ParentIndex = vm.GetObjectIndex(ParentID, Context)
                        const Parent = vm.Objects[Context][ParentIndex]

                        // Unshift name if parent is insert
                        if(ParentTemplateType == 'insert') {
                            NameList.unshift(Parent.name)
                        }
                        ParentID = Parent.parent_id
                    }
                }

                const Separator = (TemplateFunction == 'passive') ? '.' : ''

                return NameList.join(Separator)
            } else {
                return ''
            }
        },

// Misc
        GenerateSelectedPortDN: function(Scope){
            const vm = this
            const Context = vm.Context
            const ObjectID = vm.StateSelected[Context].object_id
            const Face = vm.StateSelected[Context].object_face
            const PartitionAddress = vm.StateSelected[Context].partition[Face]
            const PortIndex = vm.StateSelected[Context].port_id[Face]

            let SelectedPortDN = ''

            if(ObjectID !== null) {

                const Partition = vm.GetPartitionSelected(Context)
                if(Partition.type == "connectable") {
                    SelectedPortDN = vm.GenerateDN(Scope, ObjectID, Face, PartitionAddress, PortIndex)
                }
            }

            return SelectedPortDN
        },
        GenerateLocationDN: function(Context, LocationID){
            const vm = this
            let DNArray = []

            while(LocationID !== 0) {

                // Retrieve location data
                const LocationIndex = vm.GetLocationIndex(LocationID, Context)
                if(LocationIndex === -1) {

                    // Location not found, return 'N/A and exit while loop
                    DNArray.unshift('N/A')
                    LocationID = 0
                } else {

                    // Append location name to DNArray
                    const Location = vm.Locations[Context][LocationIndex]
                    const LocationName = Location.name

                    // Prepend DNArray with parent object
                    DNArray.unshift(LocationName)

                    // Updata location ID
                    LocationID = Location.parent_id
                }
            }

            return DNArray.join('.')
        },
        GenerateDN: function(Scope, ObjectID, Face, PartitionAddress, PortIndex=0){
            const vm = this
            const Context = 'actual'
            let DNArray = []

            // Get object
            const ObjectIndex = vm.GetObjectIndex(ObjectID, Context)
            if(ObjectIndex !== -1) {
                const Object = vm.Objects[Context][ObjectIndex]
                const ObjectName = Object.name

                // Get template
                const Template = vm.GetTemplate({ObjectID, Context})
                /*
                const TemplateID = Object.template_id
                const TemplateIndex = vm.GetTemplateIndex(TemplateID, Context)
                const Template = vm.Templates[Context][TemplateIndex]
                */
                const TemplateFunction = Template.function

                // Get template partition
                const Blueprint = Template.blueprint[Face]
                const Partition = vm.GetPartition(Blueprint, PartitionAddress)

                // Retrieve port data
                const PortFormat = Partition.port_format
                const PortTotal = Partition.port_layout.cols * Partition.port_layout.rows
                const FirstPort = vm.GeneratePortID(0, PortTotal, PortFormat)
                const LastPort = vm.GeneratePortID(PortTotal-1, PortTotal, PortFormat)
                const SelectedPort = vm.GeneratePortID(PortIndex, PortTotal, PortFormat)

                let ObjectParentID = Object.parent_id
                let LocationID = Object.location_id
                let ObjectArray = [ObjectName]
                while(ObjectParentID !== null) {

                    // Retrieve parent data
                    const ObjectParentIndex = vm.GetObjectIndex(ObjectParentID, Context)
                    const ObjectParent = vm.Objects[Context][ObjectParentIndex]
                    const ObjectParentName = ObjectParent.name

                    // Prepend DNArray with parent object
                    ObjectArray.unshift(ObjectParentName)

                    // Update parent ID and location ID
                    ObjectParentID = ObjectParent.parent_id
                    LocationID = ObjectParent.location_id
                }

                if(TemplateFunction == 'endpoint') {
                    const InsertObjectArray = ObjectArray.filter((element, index) => {return (index == 0) ? false : true})
                    const InsertObjectName = InsertObjectArray.join('')
                    if(Scope == 'trunk') {
                        DNArray.unshift(InsertObjectName+FirstPort+'-'+InsertObjectName+LastPort)
                    } else {
                        DNArray.unshift(InsertObjectName+SelectedPort)
                    }
                    DNArray.unshift(ObjectArray[0])
                } else {
                    if(Scope == 'trunk') {
                        DNArray = ObjectArray.concat([FirstPort+'-'+LastPort])
                    } else {
                        DNArray = ObjectArray.concat([SelectedPort])
                    }
                }

                const LocationDN = vm.GenerateLocationDN(Context, LocationID)
                DNArray.unshift(LocationDN)

                return DNArray.join('.')
            } else {
                return 'None'
            }
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
            } else if(NodeType == 'object') {
                Icon = "HardDriveIcon"
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
                    return (TemplateType == 'standard') ? 'front' : Value
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

            if(errData.response.status == 401) {
                this.$router.push('login')
            } else {
                const errMsg = errData.response.data

                // Display error to user via toast
                this.$bvToast.toast(JSON.stringify(errMsg), {
                    title: 'Error',
                    variant: 'danger',
                })
            }
        },
        DisplaySuccess: function(Msg) {

            // Display error to user via toast
            this.$bvToast.toast(Msg, {
                title: 'Success',
                variant: 'success',
            })
        },
        ConvertTimestampToDate: function(UnixTimestamp) {
            
            const DateObj = new Date(UnixTimestamp * 1000)
            const DateStr = DateObj.toLocaleString()
            
            return DateStr
        },
        GetCablePathIndex: function(CablePathID, Context) {

            // Initial variables
            const vm = this

            // Get index
            const CablePathIndex = vm.CablePaths[Context].findIndex((CablePath) => CablePath.id == CablePathID )

            return CablePathIndex
        },
        ConvertCmToFeet: function(cm) {
            return Math.round(cm*0.0328)
        },
        ConvertCmToMeters: function(cm) {
            return Math.round(cm*0.01)
        }
    }
}