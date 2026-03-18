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
      listViewJson: {},

      jsonData: {
        name: { editorType: "dxTextBox", name: "name", label: "Name" },
        salary: { editorType: "Currency", name: "salary", label: "Salary" },
        bonus: { editorType: "Currency", name: "bonus", label: "Bonus" },
        joining_date: { editorType: "dxDateBox", name: "joining_date", label: "Joining Date" },
        experience: { editorType: "dxNumberBox", name: "experience", label: "Experience" },
        website: { editorType: "dxTextBox", name: "website", label: "Website" },
        linkedin: { editorType: "dxTextBox", name: "linkedin", label: "LinkedIn Profile" },
        github: { editorType: "dxTextBox", name: "github", label: "Github Profile" },
        notes: { editorType: "dxTextArea", name: "notes", label: "Notes" },
        twitter: { editorType: "dxTextBox", name: "twitter", label: "Twitter" }
      }
    }
  },

  mounted() {
    this.container1 = Object.values(this.jsonData)
    this.loadListView()
    this.loadEditView()   // ✅ IMPORTANT
  },

  methods: {

    /* ---------------- DRAG HANDLERS ---------------- */

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
      const item = e.fromData.splice(e.fromIndex, 1)[0]

      // duplicate prevent
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

    /* ---------------- SAVE HANDLER ---------------- */

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
      const result = {}

      this.container2.forEach(item => {
        result[item.name] = item
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
      }).then(() => alert("List View Saved Successfully"))
    },

    loadListView() {
      fetch("/get-listview")
        .then(res => res.json())
        .then(data => {
          this.listViewJson = data || {}

          this.container2 = Object.values(this.listViewJson).filter(
            i => i && i.name && i.label
          )

          const savedFields = Object.keys(this.listViewJson)

          this.container1 = Object.values(this.jsonData).filter(
            i => !savedFields.includes(i.name)
          )
        })
    },

    /* ---------------- EDIT VIEW ---------------- */

    saveEditView() {
      const result = {}

      this.editSections.forEach(section => {
        result[section.name] = {
          columns: section.columns,
          fields: {}
        }

        section.fields.forEach(field => {
          result[section.name].fields[field.name] = field
        })
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
      }).then(() => alert("Edit View Saved Successfully"))
    },

    loadEditView() {
      fetch("/get-editview")
        .then(res => res.json())
        .then(data => {

          this.editSections = []

          Object.keys(data).forEach(name => {
            this.editSections.push({
              name: name,
              columns: data[name].columns || 1,
              fields: Object.values(data[name].fields || {})
            })
          })
        })
    }

  }
}
</script>