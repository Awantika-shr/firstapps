<template>

<!-- EDIT VIEW -->
<div class="box edit-box">

<h3>
Edit View 
<button class="save-btn1" @click="saveEditView">
Save Edit View
</button>
</h3>

<div class="input-row">

<input
class="edit-input"
v-model="sectionName"
placeholder="Enter section name"
/>

<input
class="edit-input"
type="number"
min="1"
v-model="columns"
placeholder="Columns"
/>

<button class="add-btn" @click="createSection">
Add Container
</button>

</div>
<DxSortable
  :data="sections"
  group="section-drag"
  item-selector=".section-box"
  :allow-reordering="true"
  :move-item-on-drop="false"
  @drag-end="onSectionDrag"
>

<!-- DYNAMIC SECTIONS -->
<div
v-for="(section,index ) in sections"
:key="index"
class="section-box"
>

<!-- HEADER -->
<div class="section-header">
<div>
<span class="drag-icon">⋮⋮</span>
  <!-- TEXT -->
  <h4 
    v-if="!section.isEditing"
    @click="enableEdit(section)"
    style="cursor: pointer;"
  >
    {{ section.name }}
  </h4>

  <!-- INPUT -->
  <input
    v-else
    v-model="section.name"
    @keyup.enter="disableEdit(section)"
    @click.stop
    class="rename-input"
  />

</div>

<div>
<span>Columns:</span>
<input
class="column-input"
type="number"
min="1"
v-model="section.columns"
/>
<button 
  class="empty-btn"
  @click="addEmptySpace(section)"
>
  + Empty Space
</button>
</div>
  <!-- DELETE BUTTON -->
  <button 
    class="delete-btn"
    @click="deleteSection(index)"
  >
    Delete
  </button>
</div>

<!-- SORTABLE WITH SWAP -->
<DxSortable
  tag="div"
  class="section-list"
  :data="section.fields"
  group="shared"
  item-selector=".item"

  :move-item-on-drop="false"
  :allow-reordering="true"
  :allow-drop-inside-item="false"

  @add="onAdd"
  @reorder="onAdd"

  :style="{
  display:'flex',
  flexWrap:'wrap',
  gap:'10px',
  '--cols': section.columns
}"
>

<div
  v-for="(item, i) in section.fields"
  :key="item.id || item.name"
  class="item"
  :title="item.label"
    :class="[
    { 
      'empty-item': item.isEmpty,
      'active-item': selectedItemIndex === i && selectedSection === section
    }
  ]"
  @click.stop="selectItem(section, i)"
>
  <span class="label-text">
    {{ item.label }}
  </span>
</div>

</DxSortable>

</div>

</DxSortable>

</div>

</template>

<script>

// ✅ IMPORTANT IMPORTS
import { DxSortable } from "devextreme-vue/sortable"

export default {

components:{ DxSortable },

props: ["sections"],

data(){
return{
sectionName:"",
columns:"",
selectedItemIndex: null,
selectedSection: null
}
},

methods:{

// ✅ ONLY FOR CROSS CONTAINER DRAG
onAdd(e){
this.$emit("update-edit", e)
},

//container to container drag and drop 
onSectionDrag(e){

  const fromIndex = e.fromIndex

  const container = e.toComponent?.$element?.()[0]
  if (!container || !e.event) return

  const items = Array.from(container.querySelectorAll('.section-box'))

  const mouseY = e.event.clientY

  let targetIndex = items.length - 1 // default last

  for (let i = 0; i < items.length; i++) {

    const rect = items[i].getBoundingClientRect()

    // ✅ compare with middle of element
    if (mouseY < rect.top + rect.height / 2) {
      targetIndex = i
      break
    }
  }

  if (fromIndex === targetIndex) return

  const movedItem = this.sections[fromIndex]

  // remove
  this.sections.splice(fromIndex, 1)

  // insert
  this.sections.splice(targetIndex, 0, movedItem)
},

createSection(){
if(!this.sectionName.trim()) return

this.$emit("add-section", {
name:this.sectionName.trim(),
columns:this.columns || 1,
fields:[]
})

this.sectionName=""
this.columns=""
},

saveEditView(){
this.$emit("save")
},

enableEdit(section){
  section.isEditing = true
},

disableEdit(section){
  section.isEditing = false
},
deleteSection(index){
  const section = this.sections[index]

  // ✅ emit parent ko (fields wapas bhejne ke liye)
  this.$emit("delete-section", section)

  // ✅ remove section
  this.sections.splice(index, 1)
},
selectItem(section, index) {
  this.selectedItemIndex = index
  this.selectedSection = section
},
addEmptySpace(section) {

  if (this.selectedSection !== section || this.selectedItemIndex === null) {
    alert("Select item first")
    return
  }

  const emptyItem = {
    name: "__empty__" + Date.now(),
    label: "| |",
    itemType: "empty",
    isEmpty: true
  }

  section.fields.splice(this.selectedItemIndex, 0, emptyItem)

  this.selectedItemIndex = null
  this.selectedSection = null
},

}

}
</script>