let scrollTop = document.getElementById('scrolltop');
scrollTop.addEventListener('click', function(e) {
  window.scrollTo(0,0);
})

var coll = document.getElementsByClassName("collapsible");
var i;
for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var expand = this.querySelector('.expand');
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
      expand.textContent = '(+)';
    } else {
      content.style.display = "block";
      expand.textContent = '(-)';
    }
  });
}