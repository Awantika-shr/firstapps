<template>

  <!-- NAVBAR -->
  <div class="navbar">

    <button class="nav-btn active">
      Fields
    </button>

    <button
      class="nav-btn"
      :class="{activeBtn:showListView}"
      @click="toggleListView"
    >
      List View
    </button>

    <button
      class="nav-btn"
      :class="{activeBtn:showEditView}"
      @click="toggleEditView"
    >
      Edit View
    </button>

  </div>

  <div class="main">

    <!-- Fields (Always Visible) -->
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

    <!-- RIGHT SIDE -->
    <div class="view-containers">

      <!-- LIST VIEW -->
      <div v-if="showListView" class="box">

        <h3>List View</h3>

        <button class="save-btn" @click="saveListView">
          Save
        </button>

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

      <!-- EDIT VIEW -->
      <div v-if="showEditView" class="box edit-box">

        <h3>Edit View</h3>

        <div class="input-row">

          <input
            class="edit-input"
            v-model="sectionName"
            placeholder="Enter section name"
          />

          <button class="add-btn" @click="createSection">
            Add Container
          </button>

        </div>

        <!-- DYNAMIC SECTIONS -->
        <div
          v-for="section in editSections"
          :key="section.name"
          class="section-box"
        >

          <h4>
            {{ section.name }}
          </h4>

       <div class="section-scroll">

  <DxSortable
    tag="div"
    class="section-list"
    :data="section.fields"
    group="shared"
    item-selector=".item"
    :move-item-on-drop="false"
    @add="onAdd"
    @reorder="onReorder"
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

      </div>

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

      sectionName:"",
      editSections:[],

      showListView:false,
      showEditView:false,

      listViewJson:{}

    }
  },

  mounted(){

    this.container1 = Object.values(this.jsonData)

    this.loadListView()

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
        name:this.sectionName,
        fields:[]
      })

      this.sectionName=""

    }

  }

}
</script>

<style>

.navbar{
display:flex;
gap:12px;
margin-bottom:20px;
background:#f5f7fb;
padding:10px;
border-radius:8px;
border:1px solid #ddd;
}

.nav-btn{
padding:10px 20px;
border:none;
background:#e4e7ec;
cursor:pointer;
font-weight:600;
border-radius:6px;
transition:0.2s;
}

.nav-btn:hover{
background:#d9dee7;
}

.active{
background:#4CAF50;
color:white;
}

.activeBtn{
background:#4CAF50;
color:white;
}

.main{
display:flex;
gap:40px;
}

.view-containers{
display:flex;
gap:20px;
}

.box{
width:300px;
height:400px;
border:2px dashed #ccc;
padding:10px;
}

.edit-box{
width:750px;
height:800px
}

.input-row{
display:flex;
gap:10px;
margin-bottom:10px;
}

.edit-input{
flex:1;
padding:8px;
}

.add-btn{
padding:8px 15px;
background:#4CAF50;
color:white;
border:none;
cursor:pointer;
border-radius:4px;
}

.save-btn{
margin-bottom:10px;
padding:8px 15px;
background-color: #867f7f;
border:none;
color:white;
border-radius:4px;
cursor:pointer;
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
border-radius:4px;
}

.section-box{
border:1px solid #aaa;
margin-bottom:15px;
background:#fafafa;
height:140px;
padding:8px;
border-radius:6px;
display:flex;
flex-direction:column;
}

.section-box h4{
margin-bottom:10px;
}
.section-scroll{
flex:1;
overflow-y:auto;
}

.section-list{
min-height:40px;
}

</style>