<?php

interface DeltaSyncAccessStrategy {
    public function getInitFilter($user): array;
    public function getDeltaFilter($user, $lastSyncId): array;
}
