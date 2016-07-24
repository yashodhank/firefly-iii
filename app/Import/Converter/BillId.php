<?php
/**
 * BillId.php
 * Copyright (C) 2016 thegrumpydictator@gmail.com
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

declare(strict_types = 1);

namespace FireflyIII\Import\Converter;

use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Models\Bill;
use FireflyIII\Repositories\Bill\BillRepositoryInterface;
use Log;

/**
 * Class BillId
 *
 * @package FireflyIII\Import\Converter
 */
class BillId extends BasicConverter implements ConverterInterface
{

    /**
     * @param $value
     *
     * @return Bill
     */
    public function convert($value)
    {
        $value = intval(trim($value));
        Log::debug('Going to convert using BillId', ['value' => $value]);

        if ($value === 0) {
            return new Bill;
        }

        /** @var BillRepositoryInterface $repository */
        $repository = app(BillRepositoryInterface::class, [$this->user]);

        if (isset($this->mapping[$value])) {
            Log::debug('Found bill in mapping. Should exist.', ['value' => $value, 'map' => $this->mapping[$value]]);
            $bill = $repository->find(intval($this->mapping[$value]));
            if (!is_null($bill->id)) {
                Log::debug('Found bill by ID', ['id' => $bill->id]);

                return $bill;
            }
        }

        // not mapped? Still try to find it first:
        $bill = $repository->find($value);
        if (!is_null($bill->id)) {
            Log::debug('Found bill by ID ', ['id' => $bill->id]);

            return $bill;
        }

        // should not really happen. If the ID does not match FF, what is FF supposed to do?
        return new Bill;

    }
}