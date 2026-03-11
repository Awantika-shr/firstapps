<template>
<div style="margin-bottom:20px">

  <input
    v-model="newContainerName"
    placeholder="Enter container name"
  />

  <button class="create-btn" @click="createContainer">
    Create Container
  </button>

</div>

<div class="main">

<!-- Field Box -->
<div class="box">
<h3>Fields</h3>

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


<!-- List View -->
<div class="box">

<h3>
List View
<button class="list-btn" @click="saveListView">Save</button>
</h3>

<DxSortable
tag="div"
class="list"
:data="container2"
group="shared"
item-selector=".item"
:move-item-on-drop="false"
@add="onAdd"
@reorder="onReorder"
>

<div
v-for="item in container2"
:key="item.name"
class="item"
>
{{ item.label }}
</div>

</DxSortable>

</div>


<!-- Dynamic Containers -->

<div
class="box"
v-for="(items,name) in dynamicContainers"
:key="name"
>

<h3>
{{ name }}
<button class="list-btn" @click="saveListView">Save</button>

</h3>

<DxSortable
tag="div"
class="list"
:data="dynamicContainers[name]"
group="shared"
item-selector=".item"
:move-item-on-drop="false"
@add="onAdd"
@reorder="onReorder"
>

<div
v-for="item in dynamicContainers[name]"
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

data(){
return{

jsonData:{},

container1:[],
container2:[],

listViewJson:{},

newContainerName:"",

dynamicContainers:{} 

}
},

mounted(){

this.loadFields()
this.loadListView()

},

methods:{


// CREATE NEW CONTAINER
createContainer(){

if(!this.newContainerName) return

if(this.dynamicContainers[this.newContainerName]){
alert("Container already exists")
return
}

this.dynamicContainers[this.newContainerName] = []

this.newContainerName=""

},


// DRAG ADD
onAdd(e){

const item = e.fromData.splice(e.fromIndex,1)[0]

e.toData.splice(e.toIndex,0,item)

},


// DRAG REORDER
onReorder(e){

const item = e.fromData.splice(e.fromIndex,1)[0]

e.fromData.splice(e.toIndex,0,item)

},


// SAVE BUTTON
saveListView(){

const result={}
const containers=[]


// LISTVIEW CONTAINER
this.container2.forEach(item=>{

result[item.name]={...item,container:null}

})


// DYNAMIC CONTAINERS
Object.keys(this.dynamicContainers).forEach(containerName=>{

containers.push(containerName)

this.dynamicContainers[containerName].forEach(item=>{

result[item.name]={
...item,
container:containerName
}

})

})


// SAVE CONTAINER NAMES
result["_containers"]=containers

this.listViewJson=result



// SAVE LISTVIEW JSON
fetch("/save-listview",{

method:"POST",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":document
.querySelector('meta[name="csrf-token"]')
.getAttribute("content")
},

body:JSON.stringify({data:this.listViewJson})

})
.then(res=>res.json())
.then(()=>{

console.log("listview.json saved")

})


// SAVE FIELDS JSON
const fieldsData={}

Object.values(this.jsonData).forEach(item=>{

fieldsData[item.name]=item

})


fetch("/save-fields",{

method:"POST",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":document
.querySelector('meta[name="csrf-token"]')
.getAttribute("content")
},

body:JSON.stringify({data:fieldsData})

})
.then(res=>res.json())
.then(()=>{

console.log("fields.json saved")

alert("Fields Saved Successfully")

})

},


// LOAD FIELDS
loadFields(){

fetch("/get-fields")
.then(res=>res.json())
.then(data=>{

this.jsonData=data

this.container1=Object.values(this.jsonData)

this.loadListView()

})

},


// LOAD LISTVIEW
loadListView(){

fetch("/get-listview")

.then(res=>res.json())

.then(data=>{

this.listViewJson=data || {}

this.container2=[]
this.dynamicContainers={}


// LOAD CONTAINER NAMES
const containers=this.listViewJson._containers || []

containers.forEach(name=>{

this.dynamicContainers[name] = []

})


// LOAD ITEMS
Object.entries(this.listViewJson).forEach(([key,item])=>{

if(key === "_containers") return
if(typeof item !== "object") return

if(item.container){

if(!this.dynamicContainers[item.container]){
this.dynamicContainers[item.container] = []
}

this.dynamicContainers[item.container].push(item)

}else{

this.container2.push(item)

}

})


// UNUSED FIELDS
const savedFields=Object.keys(this.listViewJson)

this.container1=Object.values(this.jsonData).filter(

item=>!savedFields.includes(item.name)

)

})

}

}

}

</script>


<style>

h3{
font-size:25px;
}

.main{
display:grid;
grid-template-columns:repeat(auto-fill,300px);
gap:40px;
}

.box{
width:300px;
height:400px;
border:2px dashed #ccc;
padding:10px;
}

.list{
height:330px;
overflow-y:auto;
}

.list-btn{
margin-left:90px;
}

.item{
background:#4CAF50;
color:white;
padding:10px;
margin:5px 0;
cursor:grab;
}

.create-btn{
cursor:pointer;
}

.delete-btn{
margin-left:5px;
background:red;
color:white;
border:none;
padding:3px 8px;
cursor:pointer;
}

</style>