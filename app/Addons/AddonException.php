<?php

namespace App\Addons;

use RuntimeException;

/**
 * Raised for validation/lifecycle problems (bad manifest, missing dependency,
 * incompatible core version, broken provider...). The admin UI shows the
 * message as-is, so keep it human readable.
 */
class AddonException extends RuntimeException {}
