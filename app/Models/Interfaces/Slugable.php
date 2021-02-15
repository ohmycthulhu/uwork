<?php


namespace App\Models\Interfaces;


interface Slugable
{
    /**
     * Get column which should be converted
     *
     * @return string
    */
    public function getSlugColumn(): string;

    /**
     * Should column be translatable
     *
     * @return boolean
    */
    public function getSlugTranslatable(): bool;
}
