<?php 
namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class EventsRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    private $em;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Events::class);
        $this->em = $registry->getManager();
        $this->logger = $logger;
    }
    
    public function saveEvent(string $title, string $description, string $location, \DateTimeInterface $start, \DateTimeInterface $end, array $googleCalendarEventIds = [])
    {
        // Vérification et adaptation du tableau googleCalendarEventIds
        if (count($googleCalendarEventIds) < 2) {
            $googleCalendarEventIds = array_pad($googleCalendarEventIds, 2, 'default_value'); // Complète avec des valeurs par défaut si nécessaire
        }

        $event = new Events();
        $event->setTitle($title);
        $event->setDescription($description);
        $event->setLocation($location);
        $event->setStart($start);
        $event->setEnd($end);

        // Stockage du tableau googleCalendarEventIds dans l'entité
        $event->setGoogleCalendarEventId($googleCalendarEventIds);
    
        $this->logger->info('Sauvegarde de l\'événement dans la base de données.');
        $this->em->persist($event);
        $this->em->flush();
        
        return $event;
    }

    public function findUpcomingEvents(\DateTime $now)
    {
        return $this->createQueryBuilder('e')
            ->where('e.start >= :now')  // Filtrer par la date de début de l'événement
            ->setParameter('now', $now)
            ->orderBy('e.start', 'ASC')  // Trier par date de début
            ->getQuery()
            ->getResult();
    }

    public function findByService($service, $userId): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.user', 'u')
            ->andWhere('u.id = :userId')
            ->where('e.services = :service')
            ->setParameter('service', $service)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function getUserEvents(int $userId, string $serviceName)
    {
        $events = $this->createQueryBuilder('e')
            ->join('e.user', 'u')
            ->andWhere('u.id = :userId')  
            ->setParameter('userId', $userId)
            ->join('e.services', 's')
            ->andWhere('s.name = :serviceName')
            ->setParameter('serviceName', $serviceName)  
            ->getQuery()
            ->getResult(); 

        usort($events, function ($event1, $event2) {
            return $event1->getStart()->getTimestamp() - $event2->getStart()->getTimestamp();
        });
        
        return $events;
    }
}
