<?php
require "config.php";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta name="csrf" content="<?= $_SESSION['csrf'] ?>">
    <meta charset="UTF-8">
    <title>Fryzjer – Backend Test UI</title>

    <meta name="csrf" content="<?= $_SESSION['csrf'] ?? '' ?>">

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 40px auto;
        }
        section {
            border: 1px solid #ccc;
            padding: 16px;
            margin-bottom: 20px;
        }
        .message {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 300px;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .message.success { background: #e6ffe6; color: #006600; }
        .message.error   { background: #ffe6e6; color: #660000; }
        .message.show    { opacity: 1; }
        pre { background: #f4f4f4; padding: 10px; }
    </style>
</head>
<body>

<h1>Fryzjer – Backend Test UI</h1>

<div id="messageBox" class="message"></div>

<!-- AUTH -->
<section>
    <h2>Authentication</h2>

    <div id="loginForm">
        <input id="login_email" placeholder="Email"><br><br>
        <input id="login_password" type="password" placeholder="Password"><br><br>
        <button onclick="login()">Login</button>
        <button onclick="requestPasswordReset()">Change password</button>
    </div>

    <div id="logoutBox" style="display:none">
        <p id="loggedInfo"></p>
        <button onclick="logout()">Logout</button>
    </div>
</section>

<?php if (isset($_SESSION['reset_user_id'])): ?>
<section>
    <h2>Set new password</h2>
    <input type="password" id="new_password" placeholder="New password"><br><br>
    <button onclick="setNewPassword()">Set password</button>
</section>
<?php endif; ?>

<!-- REGISTER -->
<section>
    <h2>Register</h2>
    <input id="reg_email" placeholder="Email"><br><br>
    <input id="reg_password" type="password" placeholder="Password"><br><br>
    <select id="reg_gender">
        <option value="male">male</option>
        <option value="female">female</option>
    </select><br><br>
    <button onclick="register()">Register</button>
</section>

<!-- AVAILABILITY -->
<section>
    <h2>Check availability</h2>
    <input type="date" id="avail_date">
    <button onclick="checkAvailability()">Check</button>
    <pre id="availability_output"></pre>
</section>

<!-- RESERVATION -->
<section>
    <h2>Make reservation</h2>
    <input type="date" id="res_date">
    <input type="time" id="res_hour" step="1800">
    <select id="res_service"></select>
    <button onclick="reserve()">Reserve</button>
</section>

<!-- ADMIN -->
<section id="adminSection" style="display:none">
    <h2>Admin – Add day off</h2>
    <input type="date" id="dayoff_date">
    <button onclick="addDayOff()">Add day off</button>
</section>

<!-- MY RESERVATIONS -->
<section>
    <h2>My reservations</h2>
    <button onclick="loadReservations()">Refresh</button>
    <pre id="my_reservations"></pre>
</section>

<!-- CANCEL -->
<section>
    <h2>Cancel reservation</h2>
    <select id="cancel_select"></select>
    <button onclick="cancelSelected()">Cancel</button>
</section>

<script>
const SERVICES = {
    male: {
        cut:   { label: "Hair cut", hours: 0.5 },
        style: { label: "Styling", hours: 1 }
    },
    female: {
        ends:  { label: "Trim ends", hours: 0.5 },
        style: { label: "Styling", hours: 1 },
        color: { label: "Coloring", hours: 2 }
    }
};

function showMessage(data) {
    const box = document.getElementById("messageBox");
    box.className = "message " + (data.success ? "success" : "error");
    box.innerText = data.message;
    box.classList.add("show");
    setTimeout(() => box.classList.remove("show"), 3000);
}

/* SESSION */
function refreshSession() {
    fetch("auth/me.php", { credentials: "same-origin" })
        .then(r => r.json())
        .then(res => {
            if (!res.data.logged) {
                loginForm.style.display = "block";
                logoutBox.style.display = "none";
                adminSection.style.display = "none";
                return;
            }

            loginForm.style.display = "none";
            logoutBox.style.display = "block";
            loggedInfo.innerText = `Logged as: ${res.data.role} (${res.data.email})`;

            adminSection.style.display =
                res.data.role === "admin" ? "block" : "none";

            const sel = document.getElementById("res_service");
            sel.innerHTML = "";
            Object.entries(SERVICES[res.data.gender]).forEach(([k,v]) => {
                const o = document.createElement("option");
                o.value = k;
                o.text  = `${v.label} (${v.hours}h)`;
                sel.appendChild(o);
            });
        });
}

/* AUTH */
function login() {
    fetch("auth/login.php", { method:"POST",
        body:new URLSearchParams({
            email: login_email.value,
            password: login_password.value
        })
    }).then(r=>r.json()).then(d=>{
        showMessage(d);
        if(d.success) refreshSession();
    });
}

function logout() {
    fetch("auth/logout.php",{credentials:"same-origin"})
        .then(r=>r.json()).then(d=>{
            showMessage(d);
            refreshSession();
        });
}

function register() {
    fetch("auth/register.php",{method:"POST",
        body:new URLSearchParams({
            email: reg_email.value,
            password: reg_password.value,
            gender: reg_gender.value
        })
    }).then(r=>r.json()).then(showMessage);
}

/* RESERVATIONS */
function checkAvailability() {
    fetch(`reservations/availability.php?date=${avail_date.value}`)
        .then(r=>r.json())
        .then(d=>{
            showMessage(d);
            availability_output.innerText = JSON.stringify(d,null,2);
        });
}

function reserve() {
    fetch("reservations/reserve.php",{method:"POST",credentials:"same-origin",
        body:new URLSearchParams({
            date: res_date.value,
            hour: res_hour.value,
            service: res_service.value
        })
    }).then(r=>r.json()).then(showMessage);
}

function loadReservations() {
    fetch("reservations/my_reservations.php",{credentials:"same-origin"})
        .then(r=>r.json())
        .then(d=>{
            if(!d.success){showMessage(d);return;}
            my_reservations.innerText="";
            cancel_select.innerHTML="";
            d.data.forEach(r=>{
                my_reservations.innerText +=
                    `${r.id} | ${r.date} ${r.hour} | ${r.email} | ${r.service_type} | ${r.duration}min\n`;
                const o=document.createElement("option");
                o.value=r.id;
                o.text=`${r.date} ${r.hour} | ${r.email}`;
                cancel_select.appendChild(o);
            });
        });
}

function cancelSelected() {
    fetch("reservations/cancel_reservation.php",{method:"POST",credentials:"same-origin",
        body:new URLSearchParams({reservation_id: cancel_select.value})
    }).then(r=>r.json()).then(d=>{
        showMessage(d);
        if(d.success) loadReservations();
    });
}

/* ADMIN */
function addDayOff() {
    fetch("admin/admin_days_off.php",{method:"POST",
        body:new URLSearchParams({date: dayoff_date.value})
    }).then(r=>r.json()).then(showMessage);
}

/* PASSWORD */
function requestPasswordReset() {
    const email=prompt("Enter your email:");
    if(!email)return;
    fetch("password/password_reset_request.php",{method:"POST",
        body:new URLSearchParams({email})
    }).then(r=>r.json()).then(showMessage);
}

function setNewPassword() {
    fetch("password/reset_password.php",{method:"POST",
        body:new URLSearchParams({password:new_password.value})
    }).then(r=>r.json()).then(d=>{
        showMessage(d);
        if(d.success) setTimeout(()=>location.reload(),1500);
    });
}
function getCSRF() {
    return document.querySelector('meta[name="csrf"]').getAttribute('content');
}

refreshSession();
</script>

</body>
</html>