

<template>

<div class="container">
<div class="card">

<h2>Student Manager</h2>

<input v-model="name" placeholder="Name">
<input v-model="email" placeholder="Email">
<input v-model="course" placeholder="Course">

<button v-if="!editId" @click="addStudent">Add Student</button>
<button v-if="editId" @click="updateStudent">Update Student</button>

<br><br>

<DxDataGrid
:data-source="students"
:show-borders="true"
:key-expr="'id'"
>

<DxSearchPanel :visible="true"/>
<DxPaging :page-size="5"/>

<DxColumn data-field="name" caption="Name"/>
<DxColumn data-field="email" caption="Email"/>
<DxColumn data-field="course" caption="Course"/>

<DxColumn caption="Action" cell-template="actionTemplate"/>

<template #actionTemplate="{data}">
<button class="edit" @click="editStudent(data.data)">Edit</button>
<button class="delete" @click="deleteStudent(data.data.id)">Delete</button>
</template>

</DxDataGrid>

</div>
</div>
</template>



<script>

import axios from "axios"

import {
DxDataGrid,
DxColumn,
DxPaging,
DxSearchPanel
} from "devextreme-vue/data-grid"

export default{

components:{
DxDataGrid,
DxColumn,
DxPaging,
DxSearchPanel
},

data(){

return{

name:"",
email:"",
course:"",
students:[],
editId:null

}

},

mounted(){
this.getStudents()
},

methods:{

getStudents(){

axios.get('/api/students')
.then(res=>{
this.students = res.data
})

},

addStudent(){

axios.post('/api/students',{
name:this.name,
email:this.email,
course:this.course
})
.then(()=>{

this.getStudents()

this.name=""
this.email=""
this.course=""

})

},

editStudent(student){

this.name = student.name
this.email = student.email
this.course = student.course
this.editId = student.id

},

updateStudent(){

axios.put('/api/students/'+this.editId,{
name:this.name,
email:this.email,
course:this.course
})
.then(()=>{

this.getStudents()

this.name=""
this.email=""
this.course=""
this.editId=null

})

},

deleteStudent(id){

axios.delete('/api/students/'+id)
.then(()=>{
this.getStudents()
})

}

}

}

</script>