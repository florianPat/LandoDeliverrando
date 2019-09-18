function switchToRegisterForm()
{
    let loginForm = document.getElementById('personLoginForm');
    let registerForm = document.getElementById('personRegisterForm');
    let switchToRegisterText = document.getElementById('personShowRegisterFormText');

    loginForm.style.display = 'none';
    registerForm.style.display = 'inline';
    switchToRegisterText.style.display = 'none';
}

(function(){
    let lastAction = document.getElementById('lastAction');
    if(lastAction !== null && lastAction.innerHTML === 'registerAction') {
        switchToRegisterForm();
    }
})();