<?php

enum PaymentStatus: string
{
    case PENDING = 'Pending';
    case COMPLETED = 'Completed';
    case FAILED = 'Failed';
    case REFUNDED = 'Refunded';
}