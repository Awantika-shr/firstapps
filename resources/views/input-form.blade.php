<div>
    <h1>
        Add New User
    </h1>
    <form action="adduser" method="post">
        @csrf
        <div>
            <h5>User Skills</h5>
            <input type="checkbox" name="skills[]" value="PHP" id="php">
            <label for="php">PHP</label>
            <input type="checkbox" name="skills[]" value="NODE" id="node">
            <label for="node">NODE</label>
            <input type="checkbox" name="skills[]" value="JAVA" id="java">
            <label for="java">JAVA</label>
        </div>

        <div>
            <h5>Gender</h5>
            <input type="radio" name="gender" value="male" id="male">
            <label for="male">Male</label>
             <input type="radio" name="gender" value="female" id="female">
            <label for="female">female</label>
        </div>
    </br>
        <div>
            <h5>City</h5>
            <select name="city">
            <option value="Delhi">Delhi</option>
            <option value="Noida">Noida</option>
            <option value="Gurgaon">Gurgaon</option>
            </select>
        </div>
        <div>
            <h5>Age</h5>
            <div>
                <input type="range" name="age" max="100">
            </div>
            <button>Add New User</button>
        </div>
    </form>    
</div>
