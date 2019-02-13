<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 2019-02-10
 * Time: 21:59
 */

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Card
{
    /**
     * @var int
     * Assert\CardScheme(schemes={"AMEX", "CHINA_UNIONPAY", "DINERS", "DISCOVER", "INSTAPAYMENT", "JCB", "LASER", "MAESTRO", "MASTERCARD", "VISA"})
     * @Assert\NotBlank()
     */
    private $number;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $expirationMonth;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $expirationYear;

    /**
     * @var string
     */
    private $cvc;

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getExpirationMonth(): string
    {
        return $this->expirationMonth;
    }

    /**
     * @param string $expirationMonth
     */
    public function setExpirationMonth(string $expirationMonth): void
    {
        $this->expirationMonth = $expirationMonth;
    }

    /**
     * @return string
     */
    public function getExpirationYear(): string
    {
        return $this->expirationYear;
    }

    /**
     * @param string $expirationYear
     */
    public function setExpirationYear(string $expirationYear): void
    {
        $this->expirationYear = $expirationYear;
    }

    /**
     * @return string
     */
    public function getCvc(): string
    {
        return $this->cvc;
    }

    /**
     * @param string $cvc
     */
    public function setCvc(string $cvc): void
    {
        $this->cvc = $cvc;
    }

    /**
     * @Assert\Callback
     */
    public function validate(ExecutionContextInterface $context, $payload)
    {
        if ($this->expirationYear < (new \DateTime())->format('Y')) {
            $context
                ->buildViolation('Year is too big')
                ->atPath('expirationYear')
                ->addViolation()
            ;
            return;
        } elseif ($this->expirationYear > (new \DateTime('+10 years'))->format('Y')) {
            $context
                ->buildViolation('Year is in past')
                ->atPath('expirationYear')
                ->addViolation()
            ;
            return;
        }
        $expirationDate = new \DateTime($this->expirationYear.'-'.$this->expirationMonth);
        if ($expirationDate < new \DateTime()) {
            $context->buildViolation('This card has expired.')
                ->addViolation();
        }
    }
}
