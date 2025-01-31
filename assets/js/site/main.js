import { contact } from '../site/contact.js';

export async function changeFragmentSite() {
    // const loadingIndicator = document.getElementById('loadingIndicator');
    
    document.body.addEventListener('click', async function (event) {
        if (event.target && event.target.classList.contains('change-fragment-site')) {
            const fragment = event.target.dataset.fragment;
            const subFragment = event.target.dataset.subfragment;

            // loadingIndicator.style.display = 'flex';

            
            const response = await fetch(`/?fragment=${fragment}&subFragment=${subFragment}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();
            // loadingIndicator.style.display = 'none';

            if (subFragment === 'service') {
                document.querySelector('#fragmentContent').addEventListener('click', function (event) {
                    if (event.target && event.target.classList.contains('btn-link')) {
                        const button = event.target.closest('.list-group-flush');
                        if (button) {
                            const btn = button.querySelector('.btn-link');
                            if (btn.classList.contains('collapsed')) {
                                button.classList.remove('selected');
                            } else {
                                button.classList.add('selected');
                            }
                        }
                    }
                });
            }

            if (subFragment === 'job') {
                document.querySelector('#fragmentContent').addEventListener('change', function (event) {
                    if (event.target && event.target.id === 'job') {
                        const customJobField = document.getElementById('customJob');
                        if (event.target.value === 'Autre') {
                            customJobField.classList.remove('d-none');
                        } else {
                            customJobField.classList.add('d-none');
                        }
                    }
                });
            }

            if (data.fragmentContent) {
                const fragmentContent = document.getElementById('fragmentContent');
                if (fragmentContent) {
                    fragmentContent.innerHTML = data.fragmentContent;
                }
            }

            if (data.subFragmentContent) {
                const subFragmentContent = document.getElementById('subFragmentContent');
                if (subFragmentContent) {
                    subFragmentContent.innerHTML = data.subFragmentContent;
                }
            }
           
        }
    });
}