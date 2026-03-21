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
      listViewJson: [], // ARRAY FORMAT
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

    onAddOrReorder(e) {
       // SAME CONTAINER → SWAP (replace behavior) 
       if (e.fromData === e.toData) { 
        const fromIndex = e.fromIndex 
        const toIndex = e.toIndex 
        // safety check 
        if (fromIndex === toIndex)
         return 
         const temp = e.fromData[fromIndex]
          // Vue reactivity safe swap 
          if (this.$set) {
             this.$set(e.fromData, fromIndex, e.fromData[toIndex]) 
             this.$set(e.fromData, toIndex, temp)
              } else { 
              e.fromData[fromIndex] = e.fromData[toIndex]
              e.fromData[toIndex] = temp
              }

              return
              } 
              //DIFFERENT CONTAINER → MOVE (no duplicate)
              const item = e.fromData.splice(e.fromIndex, 1)[0] 
              const exists = e.toData.find(i => i.name === item.name) 
              
              if (!exists) {
                e.toData.splice(e.toIndex, 0, item) 
                } 
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

      // ✅ LIST VIEW
      if (this.$route.path === "/listview") {
        const listFields = (this.listViewJson || []).map(i => i.dataField)
        this.container1 = Object.values(this.jsonData).filter(
          f => !listFields.includes(f.name)
        )
      }

      // ✅ EDIT VIEW
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