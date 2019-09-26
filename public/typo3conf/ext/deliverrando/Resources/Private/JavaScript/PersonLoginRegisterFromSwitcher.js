function switchToRegisterForm()
{
    let loginForm = document.getElementById('personLoginForm');
    let registerForm = document.getElementById('personRegisterForm');
    let switchToLoginText = document.getElementById('personShowLoginFormText');
    let switchToRegisterText = document.getElementById('personShowRegisterFormText');

    loginForm.style.display = 'none';
    registerForm.style.display = 'inline';
    switchToLoginText.style.display = 'block';
    switchToRegisterText.style.display = 'none';
}

function switchToLoginForm()
{
    let loginForm = document.getElementById('personLoginForm');
    let registerForm = document.getElementById('personRegisterForm');
    let switchToLoginText = document.getElementById('personShowLoginFormText');
    let switchToRegisterText = document.getElementById('personShowRegisterFormText');

    loginForm.style.display = 'inline';
    registerForm.style.display = 'none';
    switchToLoginText.style.display = 'none';
    switchToRegisterText.style.display = 'block';
}

(function(){
    let lastAction = document.getElementById('lastAction');
    console.assert(lastAction !== null, 'LastAction is null!');
    if(lastAction.innerHTML === 'registerAction') {
        switchToRegisterForm();
        accordionToggler('personLogin');
    } else if(lastAction.innerHTML === 'loginAction') {
        accordionToggler('personLogin');
    } else if(lastAction.innerHTML === 'indexAction') {
        const feLoginElement = document.getElementById('feLogin');
        if(feLoginElement.firstElementChild.firstElementChild.firstElementChild.innerHTML.indexOf('Login failure') !== -1) {
            accordionToggler('feLogin');
        }
    }
})();