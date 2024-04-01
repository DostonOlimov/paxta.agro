const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector(".nav-menu");
const body = document.querySelector("body");
hamburger.addEventListener("click", mobileMenu);
function mobileMenu() {
    hamburger.classList.toggle("active");
    navMenu.classList.toggle("active");
}
const navLink = document.querySelectorAll(".nav-link");
navLink.forEach((n) => n.addEventListener("click", closeMenu));
function closeMenu() {
    hamburger.classList.remove("active");
    navMenu.classList.remove("active");
}
hamburger.addEventListener("click", function () {
    if (body.classList.contains("no-scroll")) {
        // Remove the class to enable scrolling
        body.classList.remove("no-scroll");
    } else {
        // Add the class to disable scrolling
        body.classList.add("no-scroll");
    }
});
// ---------------------------------------------------------------------------------- //
let navbar = document.querySelector(".nav-bg");
let navLinks = document.querySelectorAll(".nav-link");
let lastScrollTop = 0;

window.addEventListener("scroll", function () {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    if (scrollTop > lastScrollTop) {
        navbar.style.top = "-96px";
    } else {
        navbar.style.top = "0";
    }
    if (scrollTop > 200) {
        navbar.style.backgroundColor = "#204e51";
        navbar.style.borderTopColor = "#204e51";
        navbar.style.borderBottomColor = "#204e51";
    } else {
        navbar.style.backgroundColor = "transparent";
        navbar.style.borderTopColor = "white";
        navbar.style.borderBottomColor = "white";
    }
    lastScrollTop = scrollTop;
});

// ---------------------------------------------------------------------------------- //
function changeLanguage(language) {
    document.cookie = "language=" + language + "; path=/";
    location.reload();
}

function toggleDropdown(event, dropdownId) {
    event.stopPropagation();
    let dropdown = document.getElementById(dropdownId);
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
}

document.addEventListener("click", function (event) {
    let dropdowns = document.querySelectorAll('.dropdown-for-language-picker-content');
    dropdowns.forEach(function(dropdown) {
        if (event.target.closest('.dropdown-for-language-picker') === null && dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
        }
    });
});

document.addEventListener("touchstart", function () {}, true);

// function changeLanguage(language) {
//     document.cookie = "language=" + language + "; path=/";
//     location.reload();
// }
// function changeFlag(path) {
//     document.getElementById("activeFlag").src = path;
//     localStorage.setItem("activeFlag", path);
//     document.getElementById("dropdown-for-language-picker").style.display =
//         "none";
// }
// function toggleDropdown(event) {
//     event.stopPropagation();
//     let dropdownForLanguage = document.getElementById(
//         "dropdown-for-language-picker"
//     );
//     dropdownForLanguage.style.display =
//         dropdownForLanguage.style.display === "block" ? "none" : "block";
// }
// document.addEventListener("click", function () {
//     document.getElementById("dropdown-for-language-picker").style.display =
//         "none";
// });
// document.addEventListener("touchstart", function() {}, true);
// ---------------------------------------------------------------------------------- //
document.addEventListener('click', function(event) {
    var phoneCallCircle = document.querySelector('.phoneCall-circle-for-mobile');
    var isClickInside = phoneCallCircle.contains(event.target);
    var phoneCallMenu = document.querySelector('.phoneCall-circle-for-mobile .phoneCall-menu-wrap');
    
    if (!isClickInside) {
        phoneCallMenu.style.display = "none";
    }
});

function showPhoneCallMenu(event) {
    event.stopPropagation();
    let phoneCallMenu = document.querySelector('.phoneCall-circle-for-mobile .phoneCall-menu-wrap');
    phoneCallMenu.style.display = "block";
}

// ---------------------------------------------------------------------------------- //
$(".owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    nav: true,
    navText: [
        "<i class='fa-solid fa-caret-left'></i>",
        "<i class='fa-solid fa-caret-right'></i>",
    ],
    items: 4,
    smartSpeed: 900,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplaySpeed: 1500,
    autoplayHoverPause: true,
    responsive: {
        0: {
            items: 1,
        },
        600: {
            items: 2,
        },
        1100: {
            items: 4,
        },
    },
});
// ---------------------------------------------------------------------------------- //
