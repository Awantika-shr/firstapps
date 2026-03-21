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

<!-- DRAG AREA -->
<DxSortable
tag="div"
class="section-list"
:data="section.fields"
group="shared"
item-selector=".item"
:move-item-on-drop="false"
@add="onAdd"
@reorder="onReorder"
:style="{
display:'grid',
gridTemplateColumns:`repeat(${section.columns},1fr)`,
gap:'10px'
}"
>

<div
  v-for="item in section.fields"
  :key="item.name"
  class="item"
  :title="item.label"
>
  <span>
    {{ item.label }}
  </span>
</div>

</DxSortable>

</div>

</div>

</template>

<script>
import { DxSortable } from "devextreme-vue/sortable";

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

onAdd(e){

// ✅ parent ko notify karo (IMPORTANT)
this.$emit("update-edit", e)

},

onReorder(e){

// ✅ reorder bhi parent handle kare
this.$emit("update-edit", e)

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