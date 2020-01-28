<?php namespace App\Repositories\Interfaces;

interface BannerInterface extends BaseRepositoryInterface
{
    public function uploadBannerImage($entry, $image_file);
}
