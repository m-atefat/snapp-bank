<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum TransactionType: int
{
    use EnumToArray;

    case CARD_TO_CARD = 203;
}
