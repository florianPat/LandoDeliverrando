const accordionToggler = function(elementId) {
    let elements = document.getElementsByClassName('accordionClass');
    for(let it of elements) {
        console.assert(it.hasAttribute('id'));
        if(it.getAttribute('id') === elementId) {
            if(it.style.display === 'none') {
                it.style.display = 'inline';
            } else {
                it.style.display = 'none';
            }
        } else if(it.style.display !== 'none') {
            it.style.display = 'none';
        }
    }
};