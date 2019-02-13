<?php

namespace App\Services;

use App\Entity\Invoice;
use Doctrine\Common\Persistence\ManagerRegistry;

class InvoiceService
{
    /**
     * @var StripeService
     */
    private $stripeService;
    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    public function __construct(StripeService $stripeService, ManagerRegistry $doctrine)
    {
        $this->stripeService = $stripeService;
        $this->doctrine = $doctrine;
    }

    public function payInvoice(Invoice $invoice)
    {
        $this->stripeService->payInvoice($invoice);
        $this->doctrine->getManager()->flush();

        return $invoice;
    }
}
