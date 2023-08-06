require('./bootstrap');

//Menu mobile
const openMenuMobile = document.getElementById('openMenuMobile');
const menuMobile = document.getElementById('menuMobile');
const closeMenuMobile = document.getElementById('closeMenuMobile');

openMenuMobile.addEventListener('click', function () {
    menuMobile.classList.toggle('hidden');
});
closeMenuMobile.addEventListener('click', function () {
    menuMobile.classList.toggle('hidden');
});
