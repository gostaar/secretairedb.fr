export function contact(){
    const e = document.getElementById('showContactFormBtn');
    if(e){
        e.addEventListener('click', function() {
            var contactForm = document.getElementById('contactForm');
            // Toggle visibility of the contact form
            if (contactForm.style.display === 'none') {
                contactForm.style.display = 'block';
            } else {
                contactForm.style.display = 'none';
            }
        });
    }
}