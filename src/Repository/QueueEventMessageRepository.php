<?php


namespace HalloVerden\MessengerQueueEventsBundle\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueEventsBundle\Entity\QueueEventMessage;
use Symfony\Component\Uid\Uuid;

/**
 * @method QueueEventMessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method QueueEventMessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method QueueEventMessage[]    findAll()
 * @method QueueEventMessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueueEventMessageRepository extends ServiceEntityRepository implements QueueEventMessageRepositoryInterface {

  /**
   * QueueEventMessageRepository constructor.
   *
   * @param ManagerRegistry $registry
   */
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, QueueEventMessage::class);
  }

  /**
   * @inheritDoc
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function create(QueueEventMessage $queueEventMessage): QueueEventMessage {
    $this->getEntityManager()->persist($queueEventMessage);
    $this->getEntityManager()->flush();

    return $queueEventMessage;
  }

  /**
   * @inheritDoc
   */
  public function deleteByUuidAndTransport(Uuid $uuid, string $transport): void {
    $qb = $this->getEntityManager()->createQueryBuilder();

    $qb->delete(QueueEventMessage::class, 'qem')
      ->andWhere($qb->expr()->eq('qem.uuid', ':uuid'))
      ->andWhere($qb->expr()->eq('qem.transport', ':transport'))
      ->setParameter('uuid', (string) $uuid)
      ->setParameter('transport', $transport);

    $qb->getQuery()->execute();
  }

  /**
   * @inheritDoc
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function getMessagesCount(string $transport): int {
    $qb = $this->getEntityManager()->createQueryBuilder();

    $qb->select('COUNT(qem.id)')
      ->from(QueueEventMessage::class, 'qem')
      ->andWhere($qb->expr()->eq('qem.transport', ':transport'))
      ->setParameter('transport', $transport);

    return intval($qb->getQuery()->getSingleScalarResult());
  }

  /**
   * @inheritDoc
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function getMessagesNotDelayedCount(string $transport): int {
    $qb = $this->getEntityManager()->createQueryBuilder();

    $qb->select('COUNT(qem.id)')
      ->from(QueueEventMessage::class, 'qem')
      ->andWhere($qb->expr()->eq('qem.transport', ':transport'))
      ->andWhere($qb->expr()->gt('qem.availableAt', 'qem.createdAt'))
      ->setParameter('transport', $transport);

    return intval($qb->getQuery()->getSingleScalarResult());
  }

  /**
   * @inheritDoc
   * @throws NonUniqueResultException
   */
  public function getFirstAvailableMessage(string $transport): ?QueueEventMessage {
    $qb = $this->createQueryBuilder('qem');

    $qb->addOrderBy('qem.availableAt', 'ASC')
      ->addOrderBy('qem.id', 'ASC')
      ->setMaxResults(1);


    return $qb->getQuery()->getOneOrNullResult();
  }

  /**
   * @inheritDoc
   * @throws NonUniqueResultException
   */
  public function getLastAvailableMessage(string $transport): ?QueueEventMessage {
    $qb = $this->createQueryBuilder('qem');

    $qb->addOrderBy('qem.availableAt', 'DESC')
      ->addOrderBy('qem.id', 'DESC')
      ->setMaxResults(1);


    return $qb->getQuery()->getOneOrNullResult();
  }

}
