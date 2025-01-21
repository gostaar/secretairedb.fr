export function facture() {
    const isMobile = window.innerWidth <= 768;
    // Fonction qui gère les clics et les interactions tactiles
    function handleFactureClick(e) {
        if (e.target.classList.contains('facture')) {
            handleFactureDetails(e);
        }

        if (e.target.id === 'btnPdfFacture') {
            generatePdf();
            window.appState.endLoadingState = true;
        }

        if (e.target.id === 'btnPrintFacture') {
            previewAndPrintPdf(); 
        }
    }

    if (isMobile) {
        document.addEventListener('pointerdown', handleFactureClick);
    } else {
        document.addEventListener('click', handleFactureClick);
    }
}

let factureId;

function handleFactureDetails(e) {
    e.preventDefault();
    e.stopPropagation();
    
    factureId = e.target.getAttribute('data-id');
    document.getElementById('modalContentFacture').classList.add('d-none');
    document.getElementById('loading').style.display = 'flex';
    
    fetch(`/facture/${factureId}`)
    .then(response => response.json())
    .then(data => {
        // Remplir les informations de la facture dans le modal
        document.getElementById('factureId').textContent = factureId;
        document.getElementById('factureId2').textContent = factureId;
        document.getElementById('factureDate_facture').textContent = data.date_facture;
        document.getElementById('factureDateLimite').textContent = getFactureDateLimite(data.date_facture);
        
        const factureContentTable2 = document.getElementById('factureContentTable2');
        while (factureContentTable2.firstChild) {
            factureContentTable2.removeChild(factureContentTable2.firstChild);
        }
        let totalHtva = 0;

        if (data.factureLignes && Array.isArray(data.factureLignes)) {
            data.factureLignes.forEach(function (ligne) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="text-start" style='font-size: 14px;'>${ligne.designation}</td>
                    <td class="text-end" style='font-size: 14px;'>${ligne.quantite}</td>
                    <td class="text-end" style='font-size: 14px;'>${ligne.prixUnitaire}</td>
                    <td class="text-end" style='font-size: 14px;'>${ligne.htva}</td>
                `;
                let formattedHtva = ligne.htva.replace(/\s/g, '').replace('€', '').replace(',', '.');
                formattedHtva = parseFloat(formattedHtva);
                totalHtva += formattedHtva;
                factureContentTable2.appendChild(row);
            });
        }

        const totalHtvaFormatted = new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(totalHtva);

        document.getElementById('factureMontant').textContent = totalHtvaFormatted;
        document.getElementById('factureMontant2').textContent = totalHtvaFormatted;
        
        // Cloner les lignes de facture pour remplir une autre table (si nécessaire)
        const dataToCopy = factureContentTable2.cloneNode(true);
        const factureContentTable = document.getElementById('factureContentTable');
        const tbody = factureContentTable.querySelector('tbody');
        while (tbody.firstChild) {
            tbody.removeChild(tbody.firstChild);
        }
        tbody.append(...dataToCopy.childNodes); 
        
        document.getElementById('modalContentFacture').classList.remove('d-none');
        document.getElementById('loading').style.display = 'none';
    });
}

// Génération du fichier PDF
function generatePdf() {
    const content = document.getElementById('facturehtml2print');
    content.classList.remove('d-none');
    content.classList.add('d-flex');

    const options = {
        margin: 1,
        filename: `Facture_${factureId}.pdf`,
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

        pdf.save(`Facture_${factureId}.pdf`);

    }).catch((error) => {
        console.error("Erreur lors de la génération du PDF :", error); 
    }).finally(() => {
        content.classList.add('d-none');
        content.classList.remove('d-flex');
    });
}


function previewAndPrintPdf() {
    const content = document.getElementById('facturehtml2print');
    content.classList.remove('d-none');
    content.classList.add('d-flex');

    const options = {
        margin: 1,
        filename: `Facture_${factureId}.pdf`,
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

function getFactureDateLimite(dateFacture) {
    const dateParts = dateFacture.split('-'); 
    const dateObj = new Date(dateParts[2], dateParts[1] - 1, dateParts[0]);

    dateObj.setMonth(dateObj.getMonth() + 1);

    const newDate = `${dateObj.getDate().toString().padStart(2, '0')}-${(dateObj.getMonth() + 1).toString().padStart(2, '0')}-${dateObj.getFullYear()}`;

    return newDate;
}