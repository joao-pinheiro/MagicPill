<?php
/**
 * MagicPill
 *
 * Copyright (c) 2014-2016, Joao Pinheiro
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MagicPill
 * @package    Log
 * @copyright  Copyright (c) 2014-2016 Joao Pinheiro
 * @version    1.0
 */

namespace MagicPill\Util\Log\Writer;

use MagicPill\Util\Log\Formatter\FormatterInterface;

interface WriterInterface
    /**
     * Perform writer configuration
     * @param \Traversable $config
     * @return $this
     * @throws LogWriterInvalidConfigurationFormatException
     * @throws LogWriterInvalidFormatterException
     */
    public function configure(\Traversable $config = []);

    /**
     * Defines the log level
     * @param integer $level
     * @return $this
     * @throws LogWriterInvalidLogLevelException
     */
    public function setLogLevel($level);

    /**
     * Defines the formatter to use
     * @param \MagicPill\Util\Log\Formatter\FormatterInterface $formatter
     * @return $this
     */
    public function setFormatter(FormatterInterface $formatter);

    /**
     * Retrieves the available formatter or instantiates a default one if not available
     * @return \MagicPill\Util\Log\Formatter\FormatterInterface
     */
    public function getFormatter();

    /**
     * Returns true if writer accepts this loglevel
     * @param int $level
     * @return bool
     */
    public function accept($level);

    /**
     * Prepares message for commit
     * @param array $message
     */
    public function publish(array $message);

    /**
     * Writer shutdown function
     */
    public function shutdown();
}
