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

  const fromData = e.fromData
  const toData = e.toData
  const fromIndex = e.fromIndex
  const toIndex = e.toIndex

  if (!fromData || !toData) return

  // ✅ SAME CONTAINER (UNCHANGED)
  if (fromData === toData) {

    const container = e.toComponent?.$element?.()[0]
    if (!container) return

    const items = Array.from(container.querySelectorAll('.item'))

    const mouseY = e.event?.clientY
    const mouseX = e.event?.clientX

    let targetIndex = toIndex
    let minDistance = Infinity

    items.forEach((el, index) => {
      const rect = el.getBoundingClientRect()

      const centerX = rect.left + rect.width / 2
      const centerY = rect.top + rect.height / 2

      const dx = mouseX - centerX
      const dy = mouseY - centerY

      const distance = Math.sqrt(dx * dx + dy * dy)

      if (distance < minDistance) {
        minDistance = distance
        targetIndex = index
      }
    })

    if (fromIndex === targetIndex) return

    const temp = fromData[fromIndex]

    if (this.$set) {
      this.$set(fromData, fromIndex, fromData[targetIndex])
      this.$set(fromData, targetIndex, temp)
    } else {
      fromData[fromIndex] = fromData[targetIndex]
      fromData[targetIndex] = temp
    }

    return
  }

  // ✅ DIFFERENT CONTAINER (FIXED)
  const item = fromData[fromIndex]

  const container = e.toComponent?.$element?.()[0]

  let targetIndex = toIndex

  if (container) {
    const items = Array.from(container.querySelectorAll('.item'))

    const mouseY = e.event?.clientY
    const mouseX = e.event?.clientX

    let minDistance = Infinity

    items.forEach((el, index) => {
      const rect = el.getBoundingClientRect()

      const centerX = rect.left + rect.width / 2
      const centerY = rect.top + rect.height / 2

      const dx = mouseX - centerX
      const dy = mouseY - centerY

      const distance = Math.sqrt(dx * dx + dy * dy)

      if (distance < minDistance) {
        minDistance = distance
        targetIndex = index
      }
    })
  }

  fromData.splice(fromIndex, 1)
  toData.splice(targetIndex, 0, item)
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