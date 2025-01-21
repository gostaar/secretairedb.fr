export function linkMenu(){
    const navMenus = document.querySelectorAll('#submenu3 .nav-link, #menuAcceuil, #menuAdministratif, #btnAgenda, #menuCommercial, #menuNumerique, #menuTelephone');

    navMenus.forEach(link => {
        link.addEventListener('click', function() {
            const menuToggle = document.getElementById('submenu');
            const navbarCollapse = document.getElementById('submenu3');
           
            if (navbarCollapse.classList.contains('show')) {

                menuToggle.click();
            }
        });
    });

}
