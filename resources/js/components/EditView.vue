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
>
{{ item.label }}
</div>

</DxSortable>

</div>

</div>

</template>

<script>
import { DxSortable } from "devextreme-vue/sortable";

export default {

components:{ DxSortable },

// ✅ IMPORTANT (data parent se aayega)
props: ["sections"],

data(){
return{
sectionName:"",
columns:""
}
},

methods:{

onAdd(e){
const item = e.fromData.splice(e.fromIndex,1)[0]

// duplicate prevent
const exists = e.toData.find(f => f.name === item.name)

if(!exists){
e.toData.splice(e.toIndex,0,item)
}
},

onReorder(e){
const item = e.fromData.splice(e.fromIndex,1)[0]
e.fromData.splice(e.toIndex,0,item)
},

createSection(){
if(!this.sectionName.trim()) return

// ✅ parent ko notify karo
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
}

}

}
</script>