let lastVisibleNavItem = '';

function showNavItem(id)
{
  if(lastVisibleNavItem !== '') {
    let elements = document.getElementsByClassName(lastVisibleNavItem);
    if(elements !== null) {
      for (let i = 0; i < elements.length; ++i) {
        elements[i].style.display = 'none';
      }
    }
  }

  lastVisibleNavItem = id + 'Child';
  let elements = document.getElementsByClassName(lastVisibleNavItem);
  if(elements !== null) {
    for(let i = 0; i < elements.length; ++i) {
      elements[i].style.display = 'inline';
    }
  }
}