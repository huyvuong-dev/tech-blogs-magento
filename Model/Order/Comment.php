<?php
namespace Magenest\Blogs\Model\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
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
        $orderId = 5; // Update order with the id = 5
        $comment = 'Delivery on way'; // Your content
        $status = Order::STATE_PROCESSING; // Change order status
        try {
            $order = $this->orderRepository->get($orderId);
            if ($order->canComment()) {
                $history = $this->orderHistoryFactory->create()
                    ->setStatus(!empty($status) ? $status : $order->getStatus()) // Update status when passing $comment parameter
                    ->setEntityName(\Magento\Sales\Model\Order::ENTITY)
                    ->setIsCustomerNotified(true) // Enable Notify Customer by Email
                    ->setIsVisibleOnFront(true) // Enable visible on Storefront
                    ->setComment(
                        __('Comment: %1.', $comment)
                    ); // Set your comment
                $order->addStatusHistory($history); // Add your comment to order
            }
            $this->orderRepository->save($order);
        } catch (NoSuchEntityException $exception) {
            $this->logger->error($exception->getMessage());
        }
        return $this;
    }
}
