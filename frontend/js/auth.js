// Login
document.addEventListener("DOMContentLoaded", () => {
    const loginForm = document.getElementById("loginForm");
    if(loginForm){
        loginForm.addEventListener("submit", async e => {
            e.preventDefault();
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;

            const res = await apiPost("login.php", {email, password});
            if(res.success){
                window.location.href = "dashboard.html";
            } else {
                document.getElementById("error").innerText = res.error || "Errore login";
            }
        });
    }
});
