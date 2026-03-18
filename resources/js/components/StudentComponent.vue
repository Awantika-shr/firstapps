

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

body {
    background: #f5f6fa;
    font-family: Arial;
}

.container {
    display: flex;
    justify-content: center;
    margin: 40px auto;
}

h2 {
    font-size: 30px;
}

.card {
    width: 700px;
    padding: 20px;
    background: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

input {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button {
    padding: 8px 12px;
    margin: 5px;
    border: none;
    cursor: pointer;
    border-radius: 4px;
    background: #3498db;
    color: white;
}

button:hover {
    color: white;
    background-color: #246895;
}

button.edit {
    background: #3498db;
    color: white;
}

button.edit:hover {
    color: white;
    background-color: #246895;
}

button.delete {
    background: #e74c3c;
    color: white;
}

button.delete:hover {
    background: #b03628;
    color: white;
}