#!/usr/bin/env bash

set -euo pipefail

PHP_BIN="${PHP_BIN:-php}"

"$PHP_BIN" tests/talario_schedule_resources/service_company_boundary.php
"$PHP_BIN" tests/talario_schedule_resources/shared_capacity.php
