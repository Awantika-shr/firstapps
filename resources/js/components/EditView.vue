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
v-for="section in sections"
:key="section.name"
class="section-box"
>

<!-- HEADER -->
<div class="section-header">
<h4>{{ section.name }}</h4>

<div>
<span>Columns:</span>
<input
class="column-input"
type="number"
min="1"
v-model="section.columns"
/>
</div>
</div>

<!-- ✅ SORTABLE WITH SWAP -->
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
  v-for="item in section.fields"
  :key="item.id || item.name"
  class="item"
  :title="item.label"
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
columns:""
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

}

}
</script>