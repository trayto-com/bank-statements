<?php

namespace JakubZapletal\Component\BankStatement\Parser\ABO;

use Exception;
use JakubZapletal\Component\BankStatement\Parser\ABOParser;
use JakubZapletal\Component\BankStatement\Statement\Transaction\Transaction;

class CSOBCZParser extends ABOParser
{

    private const CURRENCIES = [
        '00036' => 'AUD',
        '00124' => 'CAD',
        '00156' => 'CNY',
        '00203' => 'CZK',
        '00208' => 'DKK',
        '00978' => 'EUR',
        '00826' => 'GBP',
        '00191' => 'HRK',
        '00348' => 'HUF',
        '00756' => 'CHF',
        '00392' => 'JPY',
        '00578' => 'NOK',
        '00985' => 'PLN',
        '00946' => 'RON',
        '00643' => 'RUB',
        '00752' => 'SEK',
        '00949' => 'TRY',
        '00840' => 'USD',
    ];

    /**
     * No.| Name                      | F/V | Pos | Len | Content | Implemented
     * ---|---------------------------|-----|-----|-----|---------|
     * 1  | Record Type               |  F  | 1   | 3   | 075     | Y
     * 2  | Client account number     |  F  | 4   | 16  | int     | N
     * 3  | Counter-account number    |  F  | 20  | 16  | int     | Y
     * 4  | Identification            |  F  | 36  | 13  | string  | Y
     * 5  | Amount                    |  F  | 49  | 12  | int     | Y
     * 6  | Posting code              |  F  | 61  | 1   | int     | Y
     * 7  | Variable symbol           |  F  | 62  | 10  | int     | Y
     * 8  | Filler                    |  F  | 72  | 2   | 00      | Y
     * 9  | Counter-account bank code |  F  | 74  | 4   | int     | Y
     * 10 | Constant symbol           |  F  | 78  | 4   | int     | Y
     * 11 | Specific symbol           |  F  | 82  | 10  | int     | Y
     * 12 | Date                      |  F  | 92  | 6   | ddmmyy  | Y
     * 13 | Note                      |  F  | 98  | 20  | string  | Y
     * 14 | Currency code             |  F  | 118 | 5   | int     | N
     * 15 | Posting date              |  F  | 123 | 4   | ddmmyy  | N
     *
     * @param string $line
     * @throws Exception
     * @return Transaction
     */
    protected function parseTransactionLine($line)
    {
        $transaction = parent::parseTransactionLine($line);

        # Currency
        $currencyCode = substr($line, 117, 5);
        $currency = $this->findCurrencyByCode($currencyCode);
        $transaction->setCurrency($currency);
        return $transaction;
    }

    /**
     * @param string $currencyCode
     * @throws Exception
     * @return string
     */
    private function findCurrencyByCode(string $currencyCode): string
    {
        if (!array_key_exists($currencyCode, self::CURRENCIES)) {
            throw new Exception('Unknown currency with code ' . $currencyCode);
        }

        return self::CURRENCIES[$currencyCode];
    }

}
