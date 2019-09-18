(function ()
{
    let operationMsg = document.getElementById('operationMsg');
    if(operationMsg !== null) {
        setTimeout(() => operationMsg.style.visibility = 'collapse', 2000);
        setTimeout(() => document.getElementById('redirectLinkThing').click(), 2100);
    }
})();