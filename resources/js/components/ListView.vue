<template>
 <!-- RIGHT SIDE -->
 <div class="view-containers">

   <!-- LIST VIEW -->
   <div class="box">

     <h3>
       List View 
       <button class="save-btn" @click="saveListView">
         Save
       </button>
     </h3>

     <DxSortable
       tag="div"
       class="list"
       :data="items"
       group="shared"
       item-selector=".item"
       :move-item-on-drop="false"
       @add="onAdd"
       @reorder="onReorder"
     >
       <div
         v-for="item in items"
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

// ✅ IMPORTANT (parent se data aayega)
props: ["items"],

methods:{

onAdd(e){
   const item = e.fromData[e.fromIndex]

  const exists = e.toData.find(i => i.name === item.name)

  if(!exists){
    this.$emit("update-list", e)
  }

},

onReorder(e){
this.$emit("update-list", e)
},

saveListView(){
this.$emit("save")
}

}

}
</script>