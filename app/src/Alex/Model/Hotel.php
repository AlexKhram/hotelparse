<?php

namespace Alex\Model;

class Hotel extends Model
{
    protected $table = 'hotels';

    public function addNewHotel(array $hotel)
    {
        $affectedRow = $this->app['db']->executeUpdate(
            "INSERT IGNORE INTO {$this->table} (`name`, `address`, `desc_titel`, `desc`, `desc_geo`, `desc_review`, `desc_lang`) VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $hotel['name'],
                $hotel['address'],
                $hotel['descTitle'],
                $hotel['desc'],
                $hotel['descGeo'],
                $hotel['descReview'],
                $hotel['descLang']
            ]
        );
        if ($affectedRow === 0) {
            return '';
        }
        return $this->app['db']->lastInsertId();
    }

    public function getHotels()
    {
        $hotels = $this->app['db']->fetchAll("SELECT * FROM {$this->table}");
        foreach($hotels as &$hotel){
            $hotel['desc'] = str_replace("\n", '</br>', $hotel['desc']);
            $hotel['facilities'] = $this->app['modelFacility']->getFacilitiesForHotel($hotel['id']);
            $hotel['images'] = $this->app['modelImage']->getImagesForHotel($hotel['id']);
        }

        return $hotels;
    }
}