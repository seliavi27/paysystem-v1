<?php

enum PaymentType: string
{
    case CARD = 'Card';
    case WALLET = 'Wallet';
    case BANK_TRANSFER = 'Bank transfer';
}