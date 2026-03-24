<template>
  <div>
    <!-- NAVBAR -->
    <div class="navbar">
      <router-link
        to="/listview"
        class="nav-btn"
        :class="{ activeBtn: $route.path === '/listview' }"
      >
        List View
      </router-link>

      <router-link
        to="/editview"
        class="nav-btn"
        :class="{ activeBtn: $route.path === '/editview' }"
      >
        Edit View
      </router-link>
    </div>

    <div class="main">
      <!-- Fields panel -->
      <Fields
        :fields="container1"
        @update-fields="handleFieldsUpdate"
      />

      <!-- Router view -->
      <router-view
        :items="container2"
        :sections="editSections"
        @update-list="handleListUpdate"
        @update-edit="handleEditUpdate"
        @add-section="createSection"
        @save="handleSave"
      />
    </div>
  </div>
</template>

<script>
import Fields from './components/Fields.vue'

export default {
  components: { Fields },

  data() {
    return {
      container1: [],
      container2: [],
      editSections: [],
      listViewJson: [],
      jsonData: {}
    }
  },

  mounted() {
    this.loadFields()
    this.loadListView()
    this.loadEditView()
  },

  methods: {

    /* ---------------- LOAD MASTER ---------------- */
    loadFields() {
      fetch("/get-fields")
        .then(res => res.json())
        .then(data => {
          this.jsonData = data || {}
          this.updateModuleFields()
        })
    },

    /* ---------------- DRAG ---------------- */
    handleFieldsUpdate(e) {
      this.onAddOrReorder(e)
    },

    handleListUpdate(e) {
      this.onAddOrReorder(e)
    },

    handleEditUpdate(e) {
      this.onAddOrReorder(e)
    },

    /* FINAL FIXED LOGIC */
    onAddOrReorder(e) {

  //extract drag data 
  const fromData = e.fromData  //source array 
  const toData = e.toData     //destination array  
  const fromIndex = e.fromIndex  //original index
  const toIndex = e.toIndex     //library index      

  //safety check 
  if (!fromData || !toData) return


  //SAME CONTAINER → CUSTOM GRID SWAP
  if (fromData === toData) {

    //get DOM container 
    const container = e.toComponent?.$element?.()[0]
    if (!container) return
  
    //get all items elements 
    const items = Array.from(container.querySelectorAll('.item'))

    // get mouse drop position
    const mouseY = e.event?.clientY
    const mouseX = e.event?.clientX

    //default fallback index 
    let targetIndex = toIndex

    //track closest element 
    let minDistance = Infinity

   //FIND NEAREST ITEM USING DISTANCE FORMULA  
    items.forEach((el, index) => {

      const rect = el.getBoundingClientRect()

      //find center of each item 
      const centerX = rect.left + rect.width / 2
      const centerY = rect.top + rect.height / 2

      //distance from mouse to item center 
      const dx = mouseX - centerX
      const dy = mouseY - centerY

      //Euclidean distance formula 
      const distance = Math.sqrt(dx * dx + dy * dy)

      //Update closest item 
      if (distance < minDistance) {
        minDistance = distance
        targetIndex = index
      }
    })

    //it same position , do nothing 
    if (fromIndex === targetIndex) return

    //swap logic 
    const temp = fromData[fromIndex]

    if (this.$set) {
      //vue reactivity-safe swap
      this.$set(fromData, fromIndex, fromData[targetIndex])
      this.$set(fromData, targetIndex, temp)
    } else {
      //normal JS swap 
      fromData[fromIndex] = fromData[targetIndex]
      fromData[targetIndex] = temp
    }
    return
  }

  //DIFFERENT CONTAINER → MOVE (unchanged)
  const item = fromData[fromIndex]

  //Remove from source 
  fromData.splice(fromIndex, 1)

  //insert into target 
  toData.splice(toIndex, 0, item)
},

    /* ---------------- SECTION ---------------- */
    createSection(section) {
      this.editSections.push({
        name: section.name,
        columns: section.columns || 1,
        fields: []
      })
    },

    /* ---------------- SAVE ---------------- */
    handleSave() {
      if (this.$route.path === "/listview") {
        this.saveListView()
      }
      if (this.$route.path === "/editview") {
        this.saveEditView()
      }
    },

    /* ---------------- LIST VIEW ---------------- */
    saveListView() {
      const result = []

      this.container2.forEach(item => {
        result.push({
          dataField: item.name,
          editorType: item.editorType || "dxTextBox",
          label: {
            text: item.label || item.vname || item.name
          },
          visible: true
        })
      })

      this.listViewJson = result

      fetch("/save-listview", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content")
        },
        body: JSON.stringify({ data: this.listViewJson })
      })
      .then(() => {
        alert("List View Saved Successfully")
        this.updateModuleFields()
      })
    },

    loadListView() {
      fetch("/get-listview")
        .then(res => res.json())
        .then(data => {
          this.listViewJson = data || []

          this.container2 = this.listViewJson.map(item => {
            return {
              name: item.dataField,
              label: item.label?.text || item.dataField,
              editorType: item.editorType
            }
          })

          this.updateModuleFields()
        })
    },

    /* ---------------- EDIT VIEW ---------------- */
    saveEditView() {
      const result = []

      this.editSections.forEach(section => {
        const group = {
          name: section.name,
          itemType: "group",
          colCount: section.columns || 1,
          items: []
        }

        section.fields.forEach(field => {
          group.items.push({
            dataField: field.name,
            editorType: field.editorType || "dxTextBox",
            label: {
              text: field.label || field.vname || field.name
            }
          })
        })

        result.push(group)
      })

      fetch("/save-editview", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content")
        },
        body: JSON.stringify({ data: result })
      })
      .then(() => {
        alert("Edit View Saved Successfully")
        this.updateModuleFields()
      })
    },

    loadEditView() {
      fetch("/get-editview")
        .then(res => res.json())
        .then(data => {
          this.editSections = []

          ;(data || []).forEach(group => {
            this.editSections.push({
              name: group.name,
              columns: group.colCount || 1,
              fields: (group.items || []).map(item => {
                return {
                  name: item.dataField,
                  label: item.label?.text || item.dataField,
                  editorType: item.editorType
                }
              })
            })
          })

          this.updateModuleFields()
        })
    },

    /* ---------------- CORE ---------------- */
    updateModuleFields() {

      if (this.$route.path === "/listview") {
        const listFields = (this.listViewJson || []).map(i => i.dataField)
        this.container1 = Object.values(this.jsonData).filter(
          f => !listFields.includes(f.name)
        )
      }

      else if (this.$route.path === "/editview") {
        const editFields = []
        this.editSections.forEach(sec => {
          sec.fields.forEach(f => editFields.push(f.name))
        })
        this.container1 = Object.values(this.jsonData).filter(
          f => !editFields.includes(f.name)
        )
      }

      else {
        this.container1 = Object.values(this.jsonData)
      }
    }
  },

  watch: {
    $route() {
      this.updateModuleFields()
    }
  }
}
</script>