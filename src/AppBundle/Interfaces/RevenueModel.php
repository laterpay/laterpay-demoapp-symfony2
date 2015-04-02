<?php

namespace AppBundle\Interfaces;

interface RevenueModel
{
    const RM_PPU    = 'ppu';
    const RM_SIS    = 'sis';

    public function getRevenueModel();
    public function getPrice();
    public function getId();
    public function __toString();
}
