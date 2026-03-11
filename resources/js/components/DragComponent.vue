<template>
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
      <h3>List View</h3>

      <button @click="saveListView">Save</button>

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
  </div>
</template>

<script>
import { DxSortable } from "devextreme-vue/sortable";

export default {

  components:{ DxSortable },

  data(){
    return{

      jsonData:{
        name:{editorType:"dxTextBox",name:"name",label:"Name"},
        salary:{editorType:"Currency",name:"salary",label:"Salary"},
        bonus:{editorType:"Currency",name:"bonus",label:"Bonus"},
        joining_date:{editorType:"dxDateBox",name:"joining_date",label:"Joining Date"},
        experience:{editorType:"dxNumberBox",name:"experience",label:"Experience"},
        website:{editorType:"dxTextBox",name:"website",label:"Website"},
        linkedin:{editorType:"dxTextBox",name:"linkedin",label:"LinkedIn Profile"},
        github:{editorType:"dxTextBox",name:"github",label:"Github Profile"},
        notes:{editorType:"dxTextArea",name:"notes",label:"Notes"},
        twitter:{editorType:"dxTextBox",name:"twitter",label:"Twitter"}
      },

      container1:[],
      container2:[],
      listViewJson:{}

    }
  },

  mounted(){

    // fields load
    this.container1 = Object.values(this.jsonData)

    // saved json load from backend
    this.loadListView()

  },

  methods:{

    onAdd(e){

      const item = e.fromData.splice(e.fromIndex,1)[0]
      e.toData.splice(e.toIndex,0,item)

    },

    onReorder(e){

      const item = e.fromData.splice(e.fromIndex,1)[0]
      e.fromData.splice(e.toIndex,0,item)

    },

    // SAVE TO BACKEND
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
      .then(data=>{
        alert("List View Saved Successfully")
      })

    },

    // LOAD SAVED JSON
    loadListView(){

      fetch("/get-listview")
      .then(res=>res.json())
      .then(data=>{
        this.listViewJson = data || {}

        // ListView data
        this.container2 = Object.values(this.listViewJson)

        // saved field names
        const savedFields = Object.keys(this.listViewJson)

        // Fields box me sirf unused fields
        this.container1 = Object.values(this.jsonData).filter(
        item => !savedFields.includes(item.name)
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
  display:flex;
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

.item{
  background:#4CAF50;
  color:white;
  padding:10px;
  margin:5px 0;
  cursor:grab;
}

</style>
