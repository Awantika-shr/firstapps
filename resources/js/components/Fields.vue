<template>
 <div class="box">
   <h3>Module Fields</h3>

   <DxSortable
     tag="div"
     class="list"
     :data="fields"
     group="shared"
     item-selector=".item"
     :move-item-on-drop="false"
     @add="onAdd"
     @reorder="onReorder"
   >
     <div
       v-for="item in fields"
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

// ✅ ONLY PROP
props:["fields"],

// ❗ NO data(), NO container1, NO API call

methods:{

onAdd(e){
this.$emit("update-fields", e)
},

onReorder(e){
this.$emit("update-fields", e)
}

}

}
</script>