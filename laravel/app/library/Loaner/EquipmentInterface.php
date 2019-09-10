<?php

namespace Loaner;

interface EquipmentInterface {
    const STATUS_DEACTIVATED = -1;
    const STATUS_ACTIVE = 1;
    const STATUS_LOANED = 2;

    public function checkStatus();
} 