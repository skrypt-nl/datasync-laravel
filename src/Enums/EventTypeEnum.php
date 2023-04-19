<?php

namespace Skrypt\DeltaSync\Enums;

enum EventTypeEnum: string {
    case Create = 'C';
    case Update = 'U';
    case Delete = 'D';
}
