function loginvalidate() 
{
    var name = loginname();
    var password = loginpassword();
    if (name && password) {
        return true;
    }

    return false;

    function loginname()
    {
        var username = document.getElementById("username").value;
        var loginerror = "";
        if (username == "") {
            loginerror = "Please enter the username";
        }
        usernameerr.innerHTML = loginerror;

        return loginerror == "" ? true : false;
    }

    function loginpassword()
    {
        var password = document.getElementById("password").value;
        var loginerror = "";
        if (password == "") {
            loginerror = "Please enter the password";
        }
        passworderr.innerHTML = loginerror;

        return loginerror == "" ? true : false;
    }
}

setTimeout(function() {
    var successMessage = document.getElementById('success_message');
    var errorMessage = document.getElementById('error_message');

    if (successMessage) {
        successMessage.style.display = 'none';
    } else if (errorMessage) {
        errorMessage.style.display = 'none';
    }
}, 3000);
