<?php

/**
 * Pimcore
 *
 * This source file is available under following license:
 * - Pimcore Enterprise License (PEL)
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     PEL
 */

namespace Pimcore\Bundle\DataHubBatchImportBundle\Settings;

class PreviewData
{
    /**
     * @var array
     */
    protected $labels;

    /**
     * @var array
     */
    protected $previewData;

    /**
     * @var int
     */
    protected $recordNumber;

    /**
     * @var array
     */
    protected $mappedColumns;

    /**
     * PreviewData constructor.
     *
     * @param array $labels
     * @param array $previewData
     * @param int $recordNumber
     * @param array $mappedColumns
     */
    public function __construct(array $labels, array $previewData, int $recordNumber, array $mappedColumns = [])
    {
        $this->labels = $labels;
        $this->previewData = $previewData;
        $this->recordNumber = $recordNumber;
        $this->mappedColumns = array_flip($mappedColumns);
    }

    /**
     * @return array
     */
    public function getDataColumnHeaders(): array
    {
        $columnHeaders = [];
        foreach ($this->labels as $index => $label) {
            $columnHeaders[] = [
                'id' => (string) $index,
                'dataIndex' => (string) $index,
                'label' => $label
            ];
        }

        return $columnHeaders;
    }

    /**
     * @return array
     */
    public function getDataPreview(): array
    {
        $dataPreview = [];

        foreach ($this->previewData as $index => $attribute) {
            $dataPreview[] = [
                'dataIndex' => (string) $index,
                'label' => $this->labels[$index] ?? $index,
                'data' => $attribute,
                'mapped' => array_key_exists((string) $index, $this->mappedColumns)
            ];
        }

        return $dataPreview;
    }

    /**
     * @return int
     */
    public function getRecordNumber(): int
    {
        return $this->recordNumber;
    }

    /**
     * @return array
     */
    public function getRawData(): array
    {
        return $this->previewData;
    }
}
