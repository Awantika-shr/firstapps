<template>
 <!-- Fields (Always Visible) -->
 <div class="box">
   <h3>Module Fields</h3>

   <DxSortable
     tag="div"
     class="list"
     :data="container1"
     group="shared"
     item-selector=".item"
     :move-item-on-drop="false"
     @add="onAdd"
     @reorder="onReorder"
   >
     <div
       v-for="item in container1"
       :key="item.name"
       class="item"
     >
       {{ item.label }}
     </div>
   </DxSortable>

 </div>
</template>


<script>
import { DxSortable } from "devextreme-vue/sortable";

export default {

components:{ DxSortable },

data(){
return{

jsonData:{},   // ❗ fields backend se aayenge

container1:[],
container2:[],

sectionName:"",
editSections:[],

showListView:false,
showEditView:false,

listViewJson:{}

}
},

mounted(){

// ✅ fields.json load hoga
this.loadFields()

const savedList = localStorage.getItem("showListView")
const savedEdit = localStorage.getItem("showEditView")

if(savedList !== null){
this.showListView = savedList === "true"
}

if(savedEdit !== null){
this.showEditView = savedEdit === "true"
}

},

methods:{

/* LOAD FIELDS FROM BACKEND */

loadFields(){

fetch("/get-fields")
.then(res=>res.json())
.then(data=>{

this.jsonData = data || {}

this.container1 = Object.values(this.jsonData)
this.loadListView()

})

},

toggleListView(){
this.showListView = !this.showListView
localStorage.setItem("showListView",this.showListView)
},

toggleEditView(){
this.showEditView = !this.showEditView
localStorage.setItem("showEditView",this.showEditView)
},

onAdd(e){
const item = e.fromData.splice(e.fromIndex,1)[0]
e.toData.splice(e.toIndex,0,item)
},

onReorder(e){
const item = e.fromData.splice(e.fromIndex,1)[0]
e.fromData.splice(e.toIndex,0,item)
},

saveListView(){

const result = {}

this.container2.forEach(item=>{
result[item.name] = item
})

this.listViewJson = result

fetch("/save-listview",{
method:"POST",
headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":document
.querySelector('meta[name="csrf-token"]')
.getAttribute("content")
},
body:JSON.stringify({
data:this.listViewJson
})
})
.then(res=>res.json())
.then(()=>{
alert("List View Saved Successfully")
})

},

loadListView(){

fetch("/get-listview")
.then(res=>res.json())
.then(data=>{

this.listViewJson = data || {}

this.container2 = Object.values(this.listViewJson).filter(
item => item && item.name && item.label
)

const savedFields = Object.keys(this.listViewJson)

this.container1 = Object.values(this.jsonData).filter(
item => !savedFields.includes(item.name)
)

})

},

createSection(){

if(!this.sectionName.trim()) return

this.editSections.push({
name:this.sectionName.trim(),
fields:[]
})

this.sectionName=""

}

}

}
</script>