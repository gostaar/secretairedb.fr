export function devis(devisId) {
    const isMobile = window.innerWidth <= 768;

    function handleDevisClick(e) {
            if (e.target.id === 'btnPdfDevis') {
            generatePdf(devisId);
            window.appState.endLoadingState = true;
        }

        if (e.target.id === 'btnPrintDevis') {
            previewAndPrintPdf(devisId); 
        }
    }

    if (isMobile) {
        document.addEventListener('pointerdown', handleDevisClick);
    } else {
        document.addEventListener('click', handleDevisClick);
    }
}

// Génération du fichier PDF
function generatePdf(devisId) {
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


function previewAndPrintPdf(devisId) {
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
