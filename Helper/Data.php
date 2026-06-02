<?php
declare(strict_types=1);

namespace MiniOrange\PDProtect\Helper;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use MiniOrange\PDProtect\Logger\Logger;

class Data extends AbstractHelper
{
    private TypeListInterface $cacheTypeList;
    private ReinitableConfigInterface $reinitableConfig;
    private Logger $pdLogger;
    private WriterInterface $configWriter;

    /**
     * Explicitly injected scope config — avoids relying on AbstractHelper::$_scopeConfig
     * which can be inaccessible in generated interceptor classes.
     */
    protected ScopeConfigInterface $pdScopeConfig;

    public function __construct(
        Context $context,
        TypeListInterface $cacheTypeList,
        ReinitableConfigInterface $reinitableConfig,
        Logger $pdLogger,
        WriterInterface $configWriter,
        ScopeConfigInterface $pdScopeConfig
    ) {
        $this->cacheTypeList    = $cacheTypeList;
        $this->reinitableConfig = $reinitableConfig;
        $this->pdLogger         = $pdLogger;
        $this->configWriter     = $configWriter;
        $this->pdScopeConfig    = $pdScopeConfig;
        parent::__construct($context);
    }

    public function log_debug(string $message): void
    {
        $this->pdLogger->debug('MO PDProtection : ' . $message);
    }

    public function flushCache(string $from = ''): void
    {
        try {
            $this->log_debug("FlushCache: Flushing config + full_page cache from: " . $from);
            $this->cacheTypeList->cleanType('config');
            $this->cacheTypeList->cleanType('full_page');
            $this->log_debug("FlushCache: Successfully flushed config and full_page caches");
        } catch (\Exception $e) {
            $this->log_debug("FlushCache: Error flushing cache: " . $e->getMessage());
        }
    }

    public function reinitConfig(): void
    {
        $this->reinitableConfig->reinit();
    }

    // ── Free / Premium feature gates ─────────────────────────

    public function isPremium(): bool
    {
        return false;
    }

    public function isPremiumVersion(): bool
    {
        return false;
    }

    public function isDataDeletionEnabled(): bool
    {
        return false;
    }

    public function isCountryFilteringEnabled(): bool
    {
        return false;
    }

    public function isAutoCleanConfigurable(): bool
    {
        return false;
    }

    public function isCustomerDataControlsFunctional(): bool
    {
        return false;
    }

    // ── Plan name ─────────────────────────────────────────────

    public function getPlanName(): string
    {
        return 'Personal Data Protection Free';
    }

    // ── Delete approval cap (free: 10 lifetime approvals) ────

    public function getDeleteApprovalLifetimeLimit(): int
    {
        return 10;
    }

    public function getDeleteApprovalCount(): int
    {
        return (int) $this->pdScopeConfig->getValue('pdprotect/free/delete_approval_count');
    }

    public function incrementDeleteApprovalCount(): void
    {
        $current = $this->getDeleteApprovalCount();
        $this->configWriter->save('pdprotect/free/delete_approval_count', $current + 1, 'default', 0);
    }

    public function hasReachedDeleteLimit(): bool
    {
        return $this->getDeleteApprovalCount() >= $this->getDeleteApprovalLifetimeLimit();
    }
}
