<?php

var_dump(
    ini_get("apc.enable_cli"),
    apc_clear_cache(),
    apc_clear_cache('user'),
    apc_clear_cache('opcode'),
    apc_cache_info(),
    apc_sma_info()
    );
