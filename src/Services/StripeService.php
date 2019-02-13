<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-10
 * Time: 20:46
 */

namespace App\Services;

use App\Entity\CheckList;
use Stripe\Customer;
use App\Entity\Invoice;
use App\Model\Card;
use App\Entity\User;
use Stripe\Token;

class StripeService
{
    private $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
    }

    public function createStripeCustomer(User $user)
    {
        $customer = Customer::create(
            [
            'description' => "Customer for {$user->getEmail()}",
            'email' => $user->getEmail(),
        ],
            ['api_key' => $this->stripeSecretKey]
        );

        return  $user->setStripeCustomerId($customer->id);
    }

    public function addCardToCustomer(Card $card, User $user)
    {
        $token = Token::create(
            [
                'card' => [
                    'number' => $card->getNumber(),
                    'exp_month' => $card->getExpirationMonth(),
                    'exp_year' => $card->getExpirationYear(),
                    'cvc' => $card->getCvc(),
                ],
            ],
            ['api_key' => $this->stripeSecretKey]
        );
        $customer = Customer::retrieve(
            $user->getStripeCustomerId(),
            ['api_key' => $this->stripeSecretKey]
        );
        $stripeCard = $customer->sources->create(['source' => $token->id]);
        $customer->default_source = $stripeCard->id;
        $customer->save();
        if ($customer->sources->total_count > 1) {
            foreach ($customer->sources->data as $stripeCard) {
                if ($stripeCard->id != $customer->default_source) {
                    $stripeCard->delete();
                }
            }
        }

        return $user;
    }

    public function payInvoice(Invoice $userInvoice)
    {
        $invoice = \Stripe\Invoice::retrieve(
            $userInvoice->getStripeInvoiceId(),
            ['api_key' => $this->stripeSecretKey]
        );
        $invoice->pay();
        $userInvoice->setStatus("paid");

        return $userInvoice;
    }

    public function createInvoiceItem(User $user, CheckList $checkList)
    {
        \Stripe\InvoiceItem::create(
            [
            'amount' => 1000,
            'currency' => 'usd',
            'customer' => $user->getStripeCustomerId(),
            'description' => 'Fee for expired list.ID:'.$checkList->getId(),
        ],
            ['api_key' => $this->stripeSecretKey]
        );
    }

    public function createInvoice(User $user)
    {
        $invoice = \Stripe\Invoice::create(
            [
            'customer' => $user->getStripeCustomerId(),
            'auto_advance' => true, /* auto-finalize this draft after ~1 hour */
            'description' => 'Fee for a checklists'
        ],
            ['api_key' => $this->stripeSecretKey]
        );


        return $invoice;
    }
}
