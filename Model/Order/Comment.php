<?php
namespace Magenest\Blogs\Model\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Psr\Log\LoggerInterface;

class Comment
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var HistoryFactory
     */
    private $orderHistoryFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        HistoryFactory $orderHistoryFactory,
        LoggerInterface $logger
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->logger = $logger;
    }

    /**
     * @param $orderId
     * @param $comment
     * @param $status
     * @return $this
     */
    public function addCommentToOrder($orderId, $comment, $status=null)
    {
        $order = null;
        try {
            $order = $this->orderRepository->get($orderId);
            if ($order->canComment()) {
                $history = $this->orderHistoryFactory->create()
                    ->setStatus(!empty($status) ? $status : $order->getStatus())
                    ->setEntityName(\Magento\Sales\Model\Order::ENTITY)
                    ->setComment(
                        __('Add comment: %1.', $comment)
                    );
                $order->addStatusHistory($history);
            }
            $order->save();
        } catch (NoSuchEntityException $exception) {
            $this->logger->error($exception->getMessage());
        }
        return $this;
    }
}
