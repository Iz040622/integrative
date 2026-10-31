// Chin Jia Jun

// Slide up effect for elements with class "effect"
function SlideUp() {
    var target = document.getElementsByClassName("effect");
    for (var i = 0; i < target.length; i++) {
        var window_height = window.innerHeight;
        if (target[i].getBoundingClientRect().top < window_height - 50) {
            target[i].classList.add("active");
        }
        else {
            target[i].classList.remove("active");
        }
    }
}
window.addEventListener("scroll", SlideUp);

// Snackbar notification
function showSnackbar(msg) {
    const snackbar = document.getElementById('snackbar');
    snackbar.innerText = msg;
    snackbar.classList.add('show');

    setTimeout(function () { 
        document.getElementById('snackbar').classList.remove('show'); 
        snackbar.innerText = "";
    }, 3000);
}