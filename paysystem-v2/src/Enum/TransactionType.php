<?php

enum TransactionType: string
{
    case INCOME = 'Income';
    case EXPENSE = 'Expense';
    case REFUND = 'Refund';
}