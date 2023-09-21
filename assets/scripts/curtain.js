let curtain = document.querySelector('#curtain');
let html = document.querySelector('html');
html.style.visibility = "visible";
html.prepend(curtain);
curtain.style.display = 'block';
window.addEventListener('load', function(){
  curtain.remove();
  let body = document.querySelector('body');
  body.style.display = "block";
});