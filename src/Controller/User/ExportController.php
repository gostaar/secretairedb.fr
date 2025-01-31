<?php
namespace App\Controller\User;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Dossier;
use App\Entity\Events;
use App\Repository\ServicesRepository;
use App\Repository\IdentifiantsRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExportController extends AbstractController
{
    private EntityManagerInterface $em;
    private ServicesRepository $servicesRepository;
    private IdentifiantsRepository $identifiantsRepository;

    public function __construct(EntityManagerInterface $em, ServicesRepository $servicesRepository, IdentifiantsRepository $identifiantsRepository)
    {
        $this->em = $em;
        $this->servicesRepository = $servicesRepository;
        $this->identifiantsRepository = $identifiantsRepository;
    }

    #[Route('/update-settings', name: 'update-settings', methods: ['POST'])]
    public function updateSettings(Request $request): Response
    {
        $serviceId = $request->request->get('setting1');
        
        if (!is_numeric($serviceId) || !$this->isValidService($serviceId)) {
            return $this->json(['error' => 'Service invalide ou introuvable'], 400);
        }

        $serviceEntity = $this->servicesRepository->find($serviceId);
        return $this->exportToExcel($serviceEntity->getName(), $serviceId);
    }

    private function isValidService(int $serviceId): bool
    {
        $service = $this->servicesRepository->find($serviceId);
        return $service && in_array($service->getName(), ['Repertoire', 'Telephonique', 'Administratif', 'Commercial', 'Numerique', 'Agenda']);
    }

    private function exportToExcel(string $service, ?int $serviceId): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;

        if (in_array($service, ['Telephonique', 'Administratif', 'Commercial', 'Numerique'])) {
            $this->exportDossiers($sheet, $serviceId, $row);
        } elseif ($service === 'Repertoire') {
            $this->exportRepertoires($sheet, $serviceId, $row);
        } elseif ($service === 'Agenda') {
            $this->exportAgenda($sheet, $serviceId, $row);
        } elseif ($service === 'Identifiants'){
            $this->exportIdentifiants($sheet, $row);
        }

        // Retourner la réponse Excel
        $writer = new Xlsx($spreadsheet);
        return $this->streamedResponse($writer);
    }

    private function exportDossiers($sheet, int $serviceId, int &$row): void
    {
        $datas = $this->em->getRepository(Dossier::class)->findByService($serviceId, $this->getUser()->getId());

        foreach ($datas as $dossier) {
            $this->setCellValueBoldDossier("A", $sheet, $row, 'Dossier');
            $this->setCellValueBoldDossier("B", $sheet, $row, $dossier->getId());
            $this->setCellValueBoldDossier("C", $sheet, $row, $dossier->getName());

            $row++;

            if ($dossier->getDocuments()) {
                $this->exportDocuments($sheet, $dossier->getDocuments(), $row);
            }

            $row++; // Espacer les dossiers
        }
    }

    private function setDossierData($sheet, $dossier, int &$row): void
    {
        $sheet->setCellValue("B{$row}", $dossier->getId());
        $sheet->setCellValue("C{$row}", $dossier->getName());
        $row++;
    }

    private function exportDocuments($sheet, $documents, int &$row): void
    {
        foreach ($documents as $document) {
            $this->setCellValueBoldDossier("B", $sheet, $row++, 'Documents:');
            $sheet->setCellValue("B{$row}", "ID");
            $sheet->setCellValue("C{$row}", "Nom");
            $sheet->setCellValue("D{$row}", "Date");
            $sheet->setCellValue("E{$row}", "Expéditeur");
            $sheet->setCellValue("F{$row}", "Destinataire");
            $sheet->setCellValue("G{$row}", "Détails");
            $row++;

            $sheet->setCellValue("B{$row}", $document->getId());
            $sheet->setCellValue("C{$row}", $document->getName());
            $sheet->setCellValue("D{$row}", $document->getDateDocument()->format('d/m/Y'));
            $sheet->setCellValue("E{$row}", $document->getExpediteur());
            $sheet->setCellValue("F{$row}", $document->getDestinataire());
            $sheet->setCellValue("G{$row}", $document->getDetails());
            $row++;

            if ($document->getImages()) {
                $this->exportImages($sheet, $document->getImages(), $row);
            }
        }
    }

    private function exportImages($sheet, $images, int &$row): void
    {
        $this->setCellValueBoldDossier("C", $sheet, $row++, 'Images:');
        $sheet->setCellValue("C{$row}", "ID");
        $sheet->setCellValue("D{$row}", "Nom");
        $sheet->setCellValue("E{$row}", "Taille");
        $sheet->setCellValue("F{$row}", "Description");
        $row++;

        foreach ($images as $image) {
            $sheet->setCellValue("C{$row}", $image->getId());
            $sheet->setCellValue("D{$row}", $image->getImageName());
            $sheet->setCellValue("E{$row}", $image->getImageSize());
            $sheet->setCellValue("F{$row}", $image->getImageDescription());
            $row++;
        }
    }

    private function exportRepertoires($sheet, int $serviceId, int &$row): void
    {
        $datas = $this->em->getRepository(Dossier::class)->findByService($serviceId, $this->getUser()->getId());

        foreach ($datas as $dossier) {
            $this->setCellValueBoldDossier("A", $sheet, $row, 'Dossier');
            $this->setCellValueBoldDossier("B", $sheet, $row, $dossier->getId());
            $this->setCellValueBoldDossier("C", $sheet, $row, $dossier->getName());

            $row++;

            if ($dossier->getRepertoires()) {
                $this->exportRepertoireDetails($sheet, $dossier->getRepertoires(), $row);
            }

            $row++; // Espacer les dossiers
        }
    }

    private function exportRepertoireDetails($sheet, $repertoires, int &$row): void
    {
        foreach ($repertoires as $repertoire) {
            $this->setCellValueBoldDossier("B", $sheet, $row++, 'Répertoires:');
            $sheet->setCellValue("B{$row}", "ID");
            $sheet->setCellValue("C{$row}", "Nom");
            $sheet->setCellValue("D{$row}", "Adresse");
            $sheet->setCellValue("E{$row}", "Code Postal");
            $sheet->setCellValue("F{$row}", "Ville");
            $sheet->setCellValue("G{$row}", "Pays");
            $sheet->setCellValue("H{$row}", "Téléphone");
            $sheet->setCellValue("I{$row}", "Mobile");
            $sheet->setCellValue("J{$row}", "Email");
            $sheet->setCellValue("K{$row}", "Siret");
            $sheet->setCellValue("L{$row}", "Nom d'entreprise");
            $row++;

            $sheet->setCellValue("B{$row}", $repertoire->getId());
            $sheet->setCellValue("C{$row}", $repertoire->getNom());
            $sheet->setCellValue("D{$row}", $repertoire->getAdresse());
            $sheet->setCellValue("E{$row}", $repertoire->getCodePostal());
            $sheet->setCellValue("F{$row}", $repertoire->getVille());
            $sheet->setCellValue("G{$row}", $repertoire->getPays());
            $sheet->setCellValue("H{$row}", $repertoire->getTelephone());
            $sheet->setCellValue("I{$row}", $repertoire->getMobile());
            $sheet->setCellValue("J{$row}", $repertoire->getEmail());
            $sheet->setCellValue("K{$row}", $repertoire->getSiret());
            $sheet->setCellValue("L{$row}", $repertoire->getNomEntreprise());
            $row++;

            if ($repertoire->getContacts()) {
                $this->exportContacts($sheet, $repertoire->getContacts(), $row);
            }
        }
    }

    private function exportContacts($sheet, $contacts, int &$row): void
    {
        $this->setCellValueBoldDossier("C", $sheet, $row++, 'Contacts:');
        $sheet->setCellValue("C{$row}", "Nom");
        $sheet->setCellValue("D{$row}", "Téléphone");
        $sheet->setCellValue("E{$row}", "Email");
        $sheet->setCellValue("F{$row}", "Role");
        $sheet->setCellValue("G{$row}", "Commentaire");
        $row++;

        foreach ($contacts as $contact) {
            $sheet->setCellValue("C{$row}", $contact->getNom());
            $sheet->setCellValue("D{$row}", $contact->getTelephone());
            $sheet->setCellValue("E{$row}", $contact->getEmail());
            $sheet->setCellValue("F{$row}", $contact->getRole());
            $sheet->setCellValue("G{$row}", $contact->getCommentaire());
            $row++;
        }
    }

    private function exportIdentifiants($sheet, int &$row): void 
    {
        $datas = $this->identifiantsRepository->getUserIdentifiants($this->getUser()->getId());

        $this->setCellValueBoldDossier("A", $sheet, $row, 'Identifiants');
        $row++;

        $sheet->setCellValue("A{$row}", "Site");
        $sheet->setCellValue("B{$row}", "Identifiant");
        $sheet->setCellValue("C{$row}", "Mot de passe");
        $row++;

        foreach ($datas as $identifiant) {

            $sheet->setCellValue("A{$row}", $identifiant->getSite());
            $sheet->setCellValue("B{$row}", $identifiant->getIdentifiant());
            $sheet->setCellValue("C{$row}", $identifiant->getPassword());

            $row++; 
        }
    }

    private function exportAgenda($sheet, int $serviceId, int &$row): void
    {
        $datas = $this->em->getRepository(Events::class)->findByService($serviceId, $this->getUser()->getId());

        $this->setCellValueBoldDossier("A", $sheet, $row, 'Events');
        $row++;

        $sheet->setCellValue("A{$row}", "ID");
        $sheet->setCellValue("B{$row}", "Titre");
        $sheet->setCellValue("C{$row}", "Description");
        $sheet->setCellValue("D{$row}", "Lieu");
        $sheet->setCellValue("E{$row}", "Début");
        $sheet->setCellValue("F{$row}", "Fin");
        $sheet->setCellValue("G{$row}", "Références Google");
        $row++;

        foreach ($datas as $events) {

            $sheet->setCellValue("A{$row}", $events->getId());
            $sheet->setCellValue("B{$row}", $events->getTitle());
            $sheet->setCellValue("C{$row}", $events->getDescription());
            $sheet->setCellValue("D{$row}", $events->getLocation());
            $sheet->setCellValue("E{$row}", $events->getStart()->format('d/m/Y'));
            $sheet->setCellValue("F{$row}", $events->getEnd()->format('d/m/Y'));
            $googleCalendarEventId = $events->getGoogleCalendarEventId();
            $sheet->setCellValue("G{$row}", implode(", ", $googleCalendarEventId));

            $row++; // Espacer les events
        }
    }

    private function setCellValueBold($sheet, int $row, string $value): void
    {
        $sheet->setCellValue("A{$row}", $value);
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
    }

    private function setCellValueBoldDossier(string $col, $sheet, int $row, string $value): void
    {
        $sheet->setCellValue("$col{$row}", $value);
        $sheet->getStyle("$col{$row}")->getFont()->setBold(true);
    }

    private function streamedResponse(Xlsx $writer): StreamedResponse
    {
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="export.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');
        return $response;
    }

    #[Route('/export-identifiants', name: 'export-identifiants')]
    public function updateIdentifiants()
    {
        $service = 'Identifiants';

        return $this->exportToExcel($service, $serviceId = null);
    }
}
