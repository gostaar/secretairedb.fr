export function linkMenu(){

    const navLinks = document.querySelectorAll('#navbarNav .nav-link, #navbarNav button');

    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            const menuToggle = document.getElementById('menu-toggle');
            const navbarCollapse = document.getElementById('navbarNav');
            
            if (navbarCollapse.classList.contains('show')) {
                menuToggle.click();
                
            }
        });
    });

}