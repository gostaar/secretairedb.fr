export function devis() {
    const isMobile = window.innerWidth <= 768;
    // Fonction qui gère les clics et les interactions tactiles
    function handleDevisClick(e) {
        if (e.target.classList.contains('devis')) {
            handleDevisDetails(e);
        }

        if (e.target.id === 'btnPdfDevis') {
            generatePdf();
            window.appState.endLoadingState = true;
        }

        if (e.target.id === 'btnPrintDevis') {
            previewAndPrintPdf(); 
        }
    }

    if (isMobile) {
        document.addEventListener('pointerdown', handleDevisClick);
    } else {
        document.addEventListener('click', handleDevisClick);
    }
}

let devisId;

function handleDevisDetails(e) {
    e.preventDefault();
    e.stopPropagation();
    
    devisId = e.target.getAttribute('data-id');
    document.getElementById('modalContentDevis').classList.add('d-none');
    document.getElementById('loading').style.display = 'flex';
    
    fetch(`/devis/${devisId}`)
    .then(response => response.json())
    .then(data => {
        // Remplir les informations du devis dans le modal
        document.getElementById('devisId').textContent = devisId;
        document.getElementById('devisId').textContent = devisId;
        document.getElementById('devisDate_devis').textContent = data.date_devis;
        document.getElementById('devisDateLimite').textContent = getDevisDateLimite(data.date_devis);
        
        const devisContentTable2 = document.getElementById('devisContentTable2');
        while (devisContentTable2.firstChild) {
            devisContentTable2.removeChild(devisContentTable2.firstChild);
        }
        let totalHtva = 0;

        if (data.devisLignes && Array.isArray(data.devisLignes)) {
            data.devisLignes.forEach(function (ligne) {
                const row = document.createElement('tr');
                
                // Formater la HTVA avant de l'ajouter au tableau
                let formattedHtva = new Intl.NumberFormat('fr-FR', {
                    style: 'currency',
                    currency: 'EUR',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(ligne.htva); // Formatage avec le symbole €
                
                // Ajouter les données au tableau
                row.innerHTML = `
                    <td class="text-start" style='font-size: 14px;'>${ligne.designation}</td>
                    <td class="text-end" style='font-size: 14px;'>${ligne.quantite}</td>
                    <td class="text-end" style='font-size: 14px;'>${ligne.prixUnitaire}</td>
                    <td class="text-end" style='font-size: 14px;'>${formattedHtva}</td>
                `;
                
                // Ajouter la HTVA dans le total (en tant que nombre brut)
                totalHtva += parseFloat(ligne.htva); // Ajoutez le montant brut (numérique)
                
                devisContentTable2.appendChild(row);
            });
        }

        const totalHtvaFormatted = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalHtva);

        document.getElementById('devisMontant').textContent = totalHtvaFormatted;
        document.getElementById('devisMontant2').textContent = totalHtvaFormatted;
        
        // Cloner les lignes de devis pour remplir une autre table (si nécessaire)
        const dataToCopy = devisContentTable2.cloneNode(true);
        const devisContentTable = document.getElementById('devisContentTable');
        const tbody = devisContentTable.querySelector('tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        tbody.append(...dataToCopy.childNodes); 
        
        document.getElementById('modalContentDevis').classList.remove('d-none');
        document.getElementById('loading').style.display = 'none';
    });
}

// Génération du fichier PDF
function generatePdf() {
    const content = document.getElementById('devishtml2print');
    content.classList.remove('d-none');
    content.classList.add('d-flex');

    const options = {
        margin: 1,
        filename: `Devis_${devisId}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 1 }, 
        jsPDF: { unit: 'cm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(options).from(content).toPdf().get('pdf')
    .then((pdf) => {
        const pageCount = pdf.internal.getNumberOfPages();
        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();

        pdf.setFontSize(12);
        for (let i = 1; i <= pageCount; i++) {
            pdf.setPage(i);
            pdf.text(`Page ${i} de ${pageCount}`, pageWidth / 2, pageHeight - 1, { align: 'center' });
        }

        pdf.save(`Devis_${devisId}.pdf`);

    }).catch((error) => {
        console.error("Erreur lors de la génération du PDF :", error); 
    }).finally(() => {
        content.classList.add('d-none');
        content.classList.remove('d-flex');
    });
}


function previewAndPrintPdf() {
    const content = document.getElementById('devishtml2print');
    content.classList.remove('d-none');
    content.classList.add('d-flex');

    const options = {
        margin: 1,
        filename: `Devis_${devisId}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: 'cm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf()
        .set(options)
        .from(content)
        .toPdf()
        .get('pdf')
        .then((pdf) => {
            const pageCount = pdf.internal.getNumberOfPages();
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();

            pdf.setFontSize(12);
            for (let i = 1; i <= pageCount; i++) {
                pdf.setPage(i);
                pdf.text(`Page ${i} de ${pageCount}`, pageWidth / 2, pageHeight - 1, { align: 'center' });
            }

            const pdfBlob = pdf.output('blob');
            const pdfUrl = URL.createObjectURL(pdfBlob);
            const previewWindow = window.open('', '_blank');

            if (previewWindow) {
                previewWindow.document.write(`
                    <html>
                        <head><title>Aperçu PDF</title></head>
                        <body>
                            <embed width="100%" height="100%" src="${pdfUrl}" type="application/pdf">
                        </body>
                    </html>
                `);
                previewWindow.document.close();

                previewWindow.onload = () => previewWindow.print();
            }
        })
        .finally(() => {
            content.classList.add('d-none');
            content.classList.remove('d-flex');
        });
}

function getDevisDateLimite(dateDevis) {
    const dateParts = dateDevis.split('-'); 
    const dateObj = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

    dateObj.setMonth(dateObj.getMonth() + 1);

    const newDate = `${dateObj.getDate().toString().padStart(2, '0')}-${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getFullYear()}`;

    return newDate;
}