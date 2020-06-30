<?php
namespace Magenest\Blogs\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action
{
    /**
     * @var \Magenest\Blogs\Model\Order\Comment
     */
    private $comment;

    public function __construct(
        Context $context,
        \Magenest\Blogs\Model\Order\Comment $comment

    ){
        $this->comment = $comment;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->comment->addCommentToOrder(3, 'change order comment');
    }
}
