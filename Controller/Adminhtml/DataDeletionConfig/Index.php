<?php
declare(strict_types=1);

namespace MiniOrange\PDProtect\Controller\Adminhtml\DataDeletionConfig;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Free version: render premium-only notice page. No form saving.
 * Premium overrides this via route priority (before="MiniOrange_PDProtect").
 */
class Index extends Action
{
    public const ADMIN_RESOURCE = 'MiniOrange_PDProtect::datadeletionconfig';

    private readonly PageFactory $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->getConfig()->getTitle()->prepend(__('Personal Data Protection'));
        return $page;
    }
}
