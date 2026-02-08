<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Advanced Lesson Scheduler</title>

<style>
body { margin:0; font-family: Arial, sans-serif; background:#f4f6f8; }
header { background:#16a34a; color:white; padding:12px 20px;}
.container { display:grid; grid-template-columns:3fr 1fr; height:calc(100vh - 60px); }
.timetable { padding:15px; overflow:auto; }
table { width:100%; border-collapse:collapse; background:white; }
th, td { border:1px solid #ccc; padding:8px; text-align:center; position:relative; }
th { background:#dcfce7; }
.options { display:none; position:absolute; top:100%; left:0; background:white; border:1px solid #ccc; width:100%; z-index:10; }
td:hover .options { display:block; }
.option { padding:6px; cursor:pointer; }
.option:hover { background:#f1f5f9; }
.selected { background:#bbf7d0; font-weight:bold; display:block; cursor:pointer; }
.requirements { padding:20px; background:white; border-left:2px solid #ddd; }
.warning { color:red; font-weight:bold; margin-top:10px; }
button:disabled { opacity:0.5; cursor:not-allowed; }

/* POPUPS */
.popup {
  display:none;
  position:fixed;
  top:50%;
  left:50%;
  transform:translate(-50%,-50%);
  background:white;
  padding:25px;
  border-radius:8px;
  box-shadow:0 4px 12px rgba(0,0,0,0.3);
  z-index:9999;
  text-align:center;
}
.popup input {
  width:90%;
  padding:8px;
  margin:8px 0;
}
.popup button {
  padding:8px 16px;
  margin-top:10px;
  background:#16a34a;
  color:white;
  border:none;
  border-radius:6px;
  cursor:pointer;
}
</style>
</head>

<body>

<header class="header">
 <img src="LOGO.png" alt="Time-Craft Logo" class="logo-img"  height="70px" width="auto">

<label>Filter by Subject:</label>
<select id="subjectFilter" onchange="applyFilters()">
  <option value="all">All Subjects</option>
  <option>Math</option><option>Albanian</option><option>English</option>
  <option>Turkish</option><option>Physics</option><option>Chemistry</option>
  <option>Biology</option><option>Citizenship</option><option>History</option>
  <option>Geography</option><option>Music</option><option>Art</option>
  <option>PE</option><option>IT</option><option>German</option>
</select>

<label style="margin-left:15px;">Filter by Teacher:</label>
<select id="teacherFilter" onchange="applyFilters()">
  <option value="all">All Teachers</option>
  <option>Demir Nasufi</option><option>Fatbardha Gjoni</option>
  <option>Alfred Abedini</option><option>Elma Hoxha</option>
  <option>Genc Balisha</option><option>Matilda Shehu</option>
  <option>Omur Uyurca</option><option>Zafer Yilmaz</option>
  <option>Jonida Stafaj</option><option>Bukurije Hyseni</option>
  <option>Qamile Ajdini</option><option>Anila Bobja</option>
  <option>Elona Selimaj</option><option>Pranvera Noka</option>
  <option>Asuela Bega</option><option>Fjorin Veliu</option>
  <option>Albiona Hoxha</option><option>Cyme Lulaj</option>
  <option>Ergi Lika</option><option>Iva Karapinjalli</option>
  <option>Julia Hoxha</option>
</select>
</header>

<div class="container">
<div class="timetable">
<table id="schedule">
<tr>
  <th>Time</th>
  <th>Monday</th><th>Tuesday</th><th>Wednesday</th>
  <th>Thursday</th><th>Friday</th>
</tr>
</table>
</div>

<div class="requirements">
<h3>Weekly Subject Requirements</h3>
<div id="hours"></div>
<div id="warnings" class="warning"></div>
</div>
</div>

<button id="continueBtn" onclick="openNamePopup()" disabled style="
  margin:20px;
  padding:12px 20px;
  font-size:16px;
  background:#16a34a;
  color:white;
  border:none;
  border-radius:6px;">
  Continue
</button>

<!-- NAME POPUP -->
<div id="namePopup" class="popup">
  <form action="save.php" method="POST">
  <h2>Enter your details</h2>
  <input 
      name="first_name" 
      placeholder="First name" 
      required
    >

    <input 
      name="last_name" 
      placeholder="Last name" 
      required
    >

    <!-- Hidden field for timetable -->
    <input 
      type="hidden" 
      name="timetable" 
      id="timetableData"
    >

     <br>

    <button type="submit">Save</button>


  </form>
</div>

<!-- CONFIRMATION POPUP -->
<div id="confirmationPopup" class="popup">
  <h2>✅ Timetable Confirmed</h2>
  <p>Your timetable has been confirmed.</p>
  <button onclick="closeConfirmation()">OK</button>
</div>

<script>
const periods=["08:10-08:55","09:10-09:55","10:00-10:45","10:50-11:35","11:40-12:25","12:55-13:40","13:45-14:30"];
const days=["Monday","Tuesday","Wednesday","Thursday","Friday"];

const limits={Math:4,Albanian:5,English:4,Physics:2,Chemistry:2,Biology:2,IT:2,History:2,Geography:2,Art:1,Music:1,Turkish:2,Citizenship:1,PE:3,German:2};

const optionsByDayPeriod={
 Monday:{0:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "English|Genc Balisha", "English|Matilda Shehu", "Geography|Asuela Bega", "History|Pranvera Noka", "Math|Fatbardha Gjoni"],1:["Albanian|Elma Hoxha", "Biology|Qamile Ajdini", "English|Genc Balisha", "Geography|Asuela Bega", "History|Pranvera Noka", "Physics|Jonida Stafaj", "Physics| Shiperie Нoxha"],2:["Albanian|Alfred Abedini", "Chemistry|Bukurije Hyseni", "History|Elona Selimaj", "Math|Demir Nasufi", "Math|Fatbardha Gjoni", "Physics| Shiperie Нoxha", "Turkish|Omur Uyurca"],3:["Albanian|Alfred Abedini", "Biology|Qamile Ajdini", "English|Genc Balisha", "IT|Iva Karapinjalli", "Math|Fatbardha Gjoni", "Music|Fjorin Veliu", "Turkish|Zafer Yilmaz"],4:["Chemistry|Bukurije Hyseni", "Citizenship|Anila Bobja", "English|Genc Balisha", "German|Julia Hoxha", "Math|Fatbardha Gjoni", "Music|Fjorin Veliu", "Turkish|Zafer Yilmaz"],5:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "Biology|Qamile Ajdini", "Citizenship|Anila Bobja", "Math|Demir Nasufi", "PE|Cyme Lulaj", "PE|Ergi Lika", "Turkish| Amela Gjeropo"],6:["Citizenship|Anila Bobja", "English|Matilda Shehu", "Geography|Asuela Bega", "IT|Iva Karapinjalli", "PE|Cyme Lulaj", "PE|Ergi Lika", "Physics| Shiperie Нoxha", "Turkish|Zafer Yilmaz", "Turkish| Amela Gjeropo"]},
  Tuesday:{ 0:["Albanian|Alfred Abedini", "Biology|Qamile Ajdini", "Art|Albiona Hoxha", "Citizenship|Anila Bobja", "History|Elona Selimaj", "Physics|Jonida Stafaj", "Turkish| Amela Gjeropo"],1:["Albanian|Elma Hoxha", "English|Genc Balisha", "Geography|Asuela Bega", "IT|Iva Karapinjalli", "Math|Demir Nasufi", "Math|Fatbardha Gjoni", "Turkish|Zafer Yilmaz"],2:["Albanian|Elma Hoxha", "Chemistry|Bukurije Hyseni", "History|Pranvera Noka", "Math|Fatbardha Gjoni", "Music|Fjorin Veliu", "PE|Cyme Lulaj", "PE|Ergi Lika"],3:["Biology|Qamile Ajdini", "English|Genc Balisha", "Geography|Asuela Bega", "German|Julia Hoxha", "PE|Cyme Lulaj", "PE|Ergi Lika", "Physics|Jonida Stafaj", "Turkish|Zafer Yilmaz"],4:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "Biology|Qamile Ajdini", "German|Julia Hoxha", "Math|Demir Nasufi", "Math|Fatbardha Gjoni", "Physics|Jonida Stafaj"],5:["Albanian|Alfred Abedini", "Chemistry|Bukurije Hyseni", "English|Genc Balisha", "IT|Iva Karapinjalli", "Math|Fatbardha Gjoni", "PE|Cyme Lulaj", "PE|Ergi Lika"],6:["Albanian|Alfred Abedini", "Art|Albiona Hoxha", "Chemistry|Bukurije Hyseni", "English|Genc Balisha", "PE|Cyme Lulaj", "PE|Ergi Lika", "Turkish|Zafer Yilmaz"]},
  Wednesday:{ 0:["Albanian|Alfred Abedini", "Chemistry|Bukurije Hyseni", "English|Matilda Shehu", "German|Julia Hoxha", "History|Elona Selimaj", "Math|Fatbardha Gjoni", "Turkish| Amela Gjeropo"],1:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "English|Matilda Shehu", "Geography|Asuela Bega", "IT|Iva Karapinjalli", "PE|Cyme Lulaj", "PE|Ergi Lika"],2:["Albanian|Alfred Abedini", "Art|Albiona Hoxha", "Geography|Asuela Bega", "German|Julia Hoxha", "History|Elona Selimaj", "Math|Demir Nasufi", "Math|Fatbardha Gjoni"],3:["Albanian|Elma Hoxha", "Art|Albiona Hoxha", "English|Genc Balisha", "German|Julia Hoxha", "Math|Fatbardha Gjoni", "Music|Fjorin Veliu", "Physics|Jonida Stafaj"],4:["Art|Albiona Hoxha", "Biology|Qamile Ajdini", "Citizenship|Anila Bobja", "English|Genc Balisha", "German|Julia Hoxha", "History|Elona Selimaj", "IT|Iva Karapinjalli"],5:["Albanian|Alfred Abedini", "Biology|Qamile Ajdini", "Chemistry|Bukurije Hyseni", "English|Genc Balisha", "IT|Iva Karapinjalli", "Math|Demir Nasufi", "Math|Fatbardha Gjoni"],6:["Albanian|Elma Hoxha", "Art|Albiona Hoxha", "Chemistry|Bukurije Hyseni", "Citizenship|Anila Bobja", "English|Genc Balisha", "Geography|Asuela Bega", "IT|Iva Karapinjalli"]},
  Thursday:{ 0:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "Biology|Qamile Ajdini", "English|Genc Balisha", "Geography|Asuela Bega", "History|Elona Selimaj", "Math|Fatbardha Gjoni"],1:["Albanian|Alfred Abedini", "German|Julia Hoxha", "IT|Iva Karapinjalli", "PE|Cyme Lulaj", "PE|Ergi Lika", "Physics| Shiperie Нoxha"],2:["Albanian|Alfred Abedini", "Biology|Qamile Ajdini", "Chemistry|Bukurije Hyseni", "Citizenship|Anila Bobja", "English|Genc Balisha", "IT|Iva Karapinjalli", "Math|Fatbardha Gjoni"],3:["Biology|Qamile Ajdini", "Chemistry|Bukurije Hyseni", "Citizenship|Anila Bobja", "German|Julia Hoxha", "IT|Iva Karapinjalli", "PE|Cyme Lulaj", "PE|Ergi Lika"], 4:["Albanian|Elma Hoxha", "Geography|Asuela Bega", "German|Julia Hoxha", "Math|Fatbardha Gjoni", "PE|Cyme Lulaj", "PE|Ergi Lika", "Physics| Shiperie Нoxha"], 5:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "English|Matilda Shehu", "English|Genc Balisha", "History|Elona Selimaj", "IT|Iva Karapinjalli", "Math|Fatbardha Gjoni"], 6:["Biology|Qamile Ajdini", "English|Genc Balisha", "English|Matilda Shehu", "German|Julia Hoxha", "History|Pranvera Noka", "Music|Fjorin Veliu", "Physics| Shiperie Нoxha"]},
  Friday:{ 0:["Albanian|Elma Hoxha", "Art|Albiona Hoxha", "Geography|Asuela Bega", "Math|Fatbardha Gjoni", "PE|Cyme Lulaj", "PE|Ergi Lika", "Physics| Shiperie Нoxha"],1:["Albanian|Alfred Abedini", "Chemistry|Bukurije Hyseni", "History|Elona Selimaj", "Math|Demir Nasufi", "PE|Cyme Lulaj", "PE|Ergi Lika", "Physics|Jonida Stafaj"], 2:["Biology|Qamile Ajdini", "Geography|Asuela Bega", "English|Genc Balisha", "IT|Iva Karapinjalli", "Math|Fatbardha Gjoni", "Turkish|Omur Uyurca", "Turkish| Amela Gjeropo"], 3:["Albanian|Alfred Abedini", "Chemistry|Bukurije Hyseni", "English|Genc Balisha", "Geography|Asuela Bega", "German|Julia Hoxha", "History|Elona Selimaj", "Math|Demir Nasufi"], 4:["Albanian|Alfred Abedini", "Chemistry|Bukurije Hyseni", "English|Genc Balisha", "English|Matilda Shehu", "German|Julia Hoxha", "Math|Fatbardha Gjoni", "Physics| Shiperie Нoxha"], 5:["Albanian|Alfred Abedini", "Albanian|Elma Hoxha", "Biology|Qamile Ajdini", "Chemistry|Bukurije Hyseni", "Geography|Asuela Bega", "IT|Iva Karapinjalli", "Turkish| Amela Gjeropo"], 6:["Albanian|Elma Hoxha", "English|Genc Balisha", "English|Matilda Shehu", "German|Julia Hoxha", "History|Elona Selimaj", "Math|Fatbardha Gjoni", "Music|Fjorin Veliu"]}
};

const table=document.getElementById("schedule");

periods.forEach((time,p)=>{
const row=document.createElement("tr");
row.innerHTML=`<td><b>${time}</b></td>`;
days.forEach(d=>{
const td=document.createElement("td");
td.dataset.day=d;
td.dataset.period=p;
const opts=optionsByDayPeriod[d][p]||[];
td.innerHTML=`Choose lesson<div class="options">${
opts.map(o=>{
const[s,t]=o.split("|");
return `<div class="option" onclick="select(this,'${s}','${t}')">${s} – ${t}</div>`;
}).join("")
}</div>`;
row.appendChild(td);
});
table.appendChild(row);
});

function select(el,s,t){
const c=el.closest("td");
c.innerHTML=`<span class="selected" data-subject="${s}" onclick="editPeriod(this)">${s} – ${t} (change)</span>`;
updateCounts();
}

function editPeriod(el){
const c=el.closest("td");
const d=c.dataset.day,p=c.dataset.period;
const opts=optionsByDayPeriod[d][p]||[];
c.innerHTML=`Choose lesson<div class="options">${
opts.map(o=>{
const[s,t]=o.split("|");
return `<div class="option" onclick="select(this,'${s}','${t}')">${s} – ${t}</div>`;
}).join("")
}</div>`;
applyFilters();
}

function updateCounts(){
const counts={};
document.querySelectorAll(".selected").forEach(s=>{
counts[s.dataset.subject]=(counts[s.dataset.subject]||0)+1;
});
const h=document.getElementById("hours");
const w=document.getElementById("warnings");
h.innerHTML=""; w.innerHTML="";
for(const s in limits){
const u=counts[s]||0;
h.innerHTML+=`<div>${s}: ${u}/${limits[s]}</div>`;
if(u>limits[s]) w.innerHTML+=`${s} exceeds limit!<br>`;
}
document.getElementById("continueBtn").disabled=!isComplete();
}

function isComplete(){
return document.querySelectorAll(".selected").length===periods.length*days.length;
}

function applyFilters(){
const sf=document.getElementById("subjectFilter").value.toLowerCase();
const tf=document.getElementById("teacherFilter").value.toLowerCase();
document.querySelectorAll(".option").forEach(o=>{
const[s,t]=o.textContent.toLowerCase().split(" – ");
o.style.display=(sf==="all"||s.includes(sf))&&(tf==="all"||t.includes(tf))?"block":"none";
});
}

function openNamePopup(){
document.getElementById("namePopup").style.display="block";
}

function confirmSchedule(){
const fn=document.getElementById("firstName").value.trim();
const ln=document.getElementById("lastName").value.trim();
if(!fn||!ln){alert("Please enter full name");return;}
document.getElementById("namePopup").style.display="none";
document.getElementById("confirmationPopup").style.display="block";
clearTable();
}

function clearTable(){
document.querySelectorAll("td[data-day]").forEach(c=>{
const d=c.dataset.day,p=c.dataset.period;
const opts=optionsByDayPeriod[d][p]||[];
c.innerHTML=`Choose lesson<div class="options">${
opts.map(o=>{
const[s,t]=o.split("|");
return `<div class="option" onclick="select(this,'${s}','${t}')">${s} – ${t}</div>`;
}).join("")
}</div>`;
});
updateCounts();
applyFilters();
}

function closeConfirmation(){
document.getElementById("confirmationPopup").style.display="none";
}

document.querySelector("#namePopup form").addEventListener("submit", function () {
  const data = [];

  document.querySelectorAll(".selected").forEach(cell => {
    const td = cell.closest("td");
    data.push({
      day: td.dataset.day,
      period: td.dataset.period,
      value: cell.textContent
    });
  });

  document.getElementById("timetableData").value = JSON.stringify(data);
});

</script>

</body>
</html>